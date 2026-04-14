# 🧪 QA TESTING STRATEGY - Ceylon Craft E-Commerce

**Document Type**: Comprehensive QA Test Plan  
**Project**: Ceylon Craft (Laravel 12 + Vue 3 E-Commerce)  
**Version**: 1.0  
**Date**: April 15, 2026  
**Status**: PRODUCTION READY  

---

## 📋 EXECUTIVE SUMMARY

This document defines the complete testing strategy for Ceylon Craft, a production-ready e-commerce platform. 

**Testing Scope**:
- ✅ Frontend (Vue 3 + Tailwind CSS)
- ✅ Backend API (Laravel 12)
- ✅ Payment Integration (Stripe)
- ✅ Order Management System
- ✅ Inventory Management
- ✅ Admin Dashboard
- ✅ Security & Performance

**Risk Assessment**: HIGH PRIORITY AREAS
1. **Payment Processing** - Financial transactions at risk
2. **Inventory Management** - Stock overselling (race condition)
3. **Order Status Updates** - State machine integrity
4. **Authentication** - Access control breach risk
5. **Cart Persistence** - Data loss risk

**Testing Timeline**: 
- Functional: 40 hours
- API: 20 hours
- Security: 15 hours
- Performance: 10 hours
- Automation: 25 hours
- **Total**: ~110 hours

---

## 🎯 1. AUTHENTICATION & USER MANAGEMENT TESTS

### TC-AUTH-001: Valid User Registration

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-001 |
| Priority | CRITICAL |
| Scenario | User successfully registers with valid credentials |
| **Test Data** | name="John Doe", email="john@example.com", password="SecurePass123!" |
| **Steps** | 1. POST /api/auth/register with valid data<br>2. Submit form<br>3. Verify response |
| **Expected** | HTTP 201 Created, user created in DB, email confirmed, can login |
| **Validations** | Email in DB, password hashed (bcrypt), created_at set |
| **Security** | Password NOT in response or logs |

---

### TC-AUTH-002: Registration - Missing Required Fields

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-002 |
| Priority | HIGH |
| Test Cases | Empty name, empty email, empty password, all empty |
| **Expected** | HTTP 422, field-level error messages returned |
| **Response** | ```{ "success": false, "data": { "name": ["required"], "email": ["required"] } }``` |

---

### TC-AUTH-003: Registration - Invalid Email (DNS Validation)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-003 |
| Priority | HIGH |
| Test Inputs | "invalid@", "user@.com", "no-at-sign.com", "user@fakedomain99999.xyz" |
| **Expected** | HTTP 422, "Email must be valid with existing domain" |
| **Implementation** | Uses Laravel `email:rfc,dns` - validates DNS MX records |
| **Security** | Prevents fake emails, ensures deliverability |

---

### TC-AUTH-004: Registration - Weak Password

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-004 |
| Priority | CRITICAL (Security) |
| Rules | Min 8 chars, uppercase + lowercase, numbers, symbols |
| **Test Cases** | 1. "password" ✗<br>2. "PASSWORD" ✗<br>3. "Pass" ✗<br>4. "Pass123!" ✓<br>5. "Abcd1234!@#$" ✓ |
| **Expected** | Only cases 4-5 succeed (201), others 422 |

---

### TC-AUTH-005: Login - Valid Credentials

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-005 |
| Priority | CRITICAL |
| **Test Data** | email="john@example.com", password="SecurePass123!" |
| **Expected** | HTTP 200, returns api_token, user object |

---

### TC-AUTH-006: Login - Wrong Password

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-006 |
| Priority | CRITICAL |
| **Expected** | HTTP 401, generic message "Invalid credentials" (no field indication) |
| **Security** | Prevents user enumeration |

---

### TC-AUTH-007: Login - Rate Limiting (Brute Force Protection)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-007 |
| Priority | CRITICAL (Security) |
| **Rate Limit** | 5 requests per minute |
| **Test** | Send 6 requests in 60 seconds |
| **Expected** | 6th request: HTTP 429 "Too Many Requests", includes Retry-After header |

---

### TC-AUTH-008: Logout

| Aspect | Detail |
|--------|--------|
| Test ID | TC-AUTH-008 |
| Priority | HIGH |
| **Steps** | 1. POST /api/auth/logout with valid token<br>2. Try to use old token on protected endpoint |
| **Expected** | Logout: HTTP 200<br>Protected: HTTP 401 "Unauthorized" |

---

## 🛍️ 2. PRODUCT MANAGEMENT TESTS

### TC-PROD-001: Fetch All Products

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PROD-001 |
| Priority | HIGH |
| **Precondition** | 50+ products in DB |
| **API** | GET /api/products |
| **Expected** | HTTP 200, paginated (12/page default), includes pagination metadata |
| **Performance** | < 500ms response |

---

### TC-PROD-002: Product Search

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PROD-002 |
| Priority | HIGH |
| **Test Cases** | 1. Search "laptop" → all laptops<br>2. Search "xyz123" → empty<br>3. Search "" → all products |
| **API** | GET /api/products?search=laptop |
| **Expected** | Case-insensitive, partial match OK |

---

### TC-PROD-003: Filter by Price Range

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PROD-003 |
| Priority | HIGH |
| **API** | GET /api/products?min_price=100&max_price=500 |
| **Test Cases** | Valid range, inverted range (max < min) |
| **Expected** | Valid: correct products<br>Inverted: empty array |

---

### TC-PROD-004: Product Detail Page

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PROD-004 |
| Priority | CRITICAL |
| **API** | GET /api/products/{slug} |
| **Test Cases** | Valid slug, invalid slug, deleted product |
| **Expected** | Valid: HTTP 200 with full product + reviews<br>Invalid: HTTP 404 |

---

### TC-PROD-005: Add Product (Admin)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PROD-005 |
| Priority | CRITICAL |
| **Authorization** | Admin only |
| **Test** | Non-admin tries POST /api/products |
| **Expected** | HTTP 403 "Forbidden" |

---

## 🛒 3. CART MANAGEMENT TESTS

### TC-CART-001: Add to Cart

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CART-001 |
| Priority | CRITICAL |
| **API** | POST /api/cart/add |
| **Payload** | { "product_id": 5, "quantity": 2 } |
| **Expected** | HTTP 201, cart updated, stock checked |

---

### TC-CART-002: Add to Cart - Out of Stock

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CART-002 |
| Priority | CRITICAL |
| **Scenario** | Product stock=5, try qty=10 |
| **Expected** | HTTP 409 CONFLICT, "Only 5 items available" |
| **Cart State** | NOT modified |

---

### TC-CART-003: Duplicate Item in Cart

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CART-003 |
| Priority | HIGH |
| **Scenario** | Add product 5 (qty=2), then add again (qty=3) |
| **Expected** | Cart shows 1 line item, qty=5 (combined, not duplicate) |

---

### TC-CART-004: Cart Persistence After Refresh

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CART-004 |
| Priority | CRITICAL (UX) |
| **Scenario** | Add items, close browser, reopen, login |
| **Expected** | Cart items persist (DB backed, not localStorage only) |

---

### TC-CART-005: Stock Deduction Race Condition

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CART-005 |
| Priority | CRITICAL (Bug-prone) |
| **Scenario** | Last item, 2 users add simultaneously |
| **Precondition** | Stock = 1 |
| **Expected** | One succeeds (stock→0), one fails (409 Out of stock)<br>Stock never negative |
| **Prevention** | DB pessimistic lock |

---

## 💳 4. CHECKOUT & PAYMENT TESTS

### TC-CHECKOUT-001: Valid Checkout

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CHECKOUT-001 |
| Priority | CRITICAL |
| **API** | POST /api/orders/checkout |
| **Payload** | shipping_address, city, postal code, phone, terms_agreed |
| **Expected** | HTTP 201, Order created, Payment intent returned, Cart cleared |

---

### TC-CHECKOUT-002: Empty Cart Checkout

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CHECKOUT-002 |
| Priority | CRITICAL |
| **Expected** | HTTP 400, "Cannot checkout with empty cart" |

---

### TC-CHECKOUT-003: Invalid Address (SQL Injection Prevention)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CHECKOUT-003 |
| Priority | CRITICAL (Security) |
| **Test Input** | "'; DROP TABLE orders; --" |
| **Expected** | HTTP 422 (invalid format), address NOT executed as SQL |

---

### TC-CHECKOUT-004: Invalid Postal Code

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CHECKOUT-004 |
| Priority | HIGH |
| **Test Cases** | "00100" ✓, "ABC123" ✗, "123" ✗ |
| **Rule** | 5-10 digits only |
| **Expected** | Valid: accepted, Invalid: HTTP 422 |

---

### TC-CHECKOUT-005: Terms Not Agreed

| Aspect | Detail |
|--------|--------|
| Test ID | TC-CHECKOUT-005 |
| Priority | CRITICAL |
| **Test** | Submit with terms_agreed=false |
| **Expected** | HTTP 422, "Must agree to terms" |

---

### TC-PAY-001: Create Payment Intent

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-001 |
| Priority | CRITICAL |
| **API** | POST /api/payments/intent |
| **Expected** | HTTP 201, returns client_secret (for Stripe) |

---

### TC-PAY-002: Invalid Amount (< $0.50 Minimum)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-002 |
| Priority | HIGH |
| **Test** | amount=25 ($0.25) |
| **Expected** | HTTP 422, "Minimum amount is $0.50" |

---

### TC-PAY-003: Confirm Payment (Test Card)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-003 |
| Priority | CRITICAL |
| **Test Card** | 4242 4242 4242 4242 |
| **Expected** | HTTP 200, payment status = succeeded, order status = confirmed |

---

### TC-PAY-004: Payment Failure (Declined Card)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-004 |
| Priority | CRITICAL |
| **Test Card** | 4000 0000 0000 0002 |
| **Expected** | HTTP 402, payment status = failed, order status = pending |

---

### TC-PAY-005: Prevent Duplicate Charges (Idempotency)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-005 |
| Priority | CRITICAL (Financial Impact) |
| **Scenario** | Network timeout, retry payment confirmation |
| **Expected** | Only ONE charge, idempotency key prevents duplicate |

---

### TC-PAY-006: Stripe Webhook - Payment Succeeded

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PAY-006 |
| Priority | CRITICAL |
| **API** | POST /api/webhooks/stripe |
| **Event** | payment_intent.succeeded |
| **Expected** | HTTP 200, payment status = succeeded, order status = confirmed |
| **Security** | Signature verified (prevents forged webhooks) |

---

## 🔐 5. SECURITY TESTING

### TC-SEC-001: SQL Injection (Login)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-001 |
| Priority | CRITICAL (Security) |
| **Payload** | email="' OR '1'='1" |
| **Expected** | HTTP 401, treated as invalid email (Eloquent ORM prevents injection) |

---

### TC-SEC-002: SQL Injection (Product Search)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-002 |
| Priority | CRITICAL |
| **Payload** | search="; DROP TABLE products; --" |
| **Expected** | Search for literal string (returns 0 results), no injection |

---

### TC-SEC-003: XSS - Stored (Review Comment)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-003 |
| Priority | CRITICAL |
| **Payload** | comment="<script>alert('XSS')</script>" |
| **Expected** | Script stored as text (not executed), escaped on output |

---

### TC-SEC-004: Privilege Escalation

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-004 |
| Priority | CRITICAL |
| **Test** | User tries POST /api/products (admin-only) |
| **Expected** | HTTP 403 "Forbidden" |

---

### TC-SEC-005: Unauthorized Access

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-005 |
| Priority | CRITICAL |
| **Test** | User A tries GET /api/orders/123 (User B's order) |
| **Expected** | HTTP 403 "Unauthorized" |

---

### TC-SEC-006: Webhook Signature Verification

| Aspect | Detail |
|--------|--------|
| Test ID | TC-SEC-006 |
| Priority | CRITICAL |
| **Test** | Send webhook without valid signature |
| **Expected** | HTTP 403 "Invalid signature" (prevents forged events) |

---

## ⚡ 6. PERFORMANCE TESTING

### TC-PERF-001: Product Listing (50 products)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PERF-001 |
| Priority | MEDIUM |
| **Expected** | < 500ms average, < 1s p95 |

---

### TC-PERF-002: Checkout Processing

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PERF-002 |
| Priority | MEDIUM |
| **API** | POST /api/orders/checkout |
| **Expected** | < 1.5s (creates order, payment intent, Stripe call) |

---

### TC-PERF-003: 50 Concurrent Checkouts

| Aspect | Detail |
|--------|--------|
| Test ID | TC-PERF-003 |
| Priority | MEDIUM |
| **Expected** | No overselling, proper queue, < 500ms per request |

---

## 📱 7. RESPONSIVE & UI TESTING

### TC-RESP-001: Mobile (320px)

| Aspect | Detail |
|--------|--------|
| Test ID | TC-RESP-001 |
| Devices | iPhone SE, Galaxy A12 |
| **Checks** | 1-2 column layout, readable text, no horizontal scroll |

---

### TC-RESP-002: Touch Targets

| Aspect | Detail |
|--------|--------|
| Test ID | TC-RESP-002 |
| Priority | MEDIUM |
| **Check** | All buttons/links >= 48x48px for mobile |

---

### TC-UX-001: Error Messages

| Aspect | Detail |
|--------|--------|
| Test ID | TC-UX-001 |
| Priority | HIGH |
| **Test** | Payment failed error |
| **Expected** | User-friendly message (NOT "Error 422"), suggests action |

---

## 🤖 8. AUTOMATED TESTING

### Backend (PHPUnit)

```bash
# Run all tests
php artisan test

# With coverage
php artisan test --coverage

# Filter by class
php artisan test --filter=AuthTest
```

### Frontend (Vitest)

```bash
# Unit tests
npm run test

# Coverage
npm run test:coverage

# Watch mode
npm run test:watch
```

### E2E (Cypress)

```bash
# Run all tests
npx cypress run

# Interactive mode
npx cypress open

# Specific test
npx cypress run --spec "cypress/e2e/checkout.cy.js"
```

---

## 📊 TEST EXECUTION SUMMARY

**Total Test Cases**: 80+  
**Estimated Time**: 30-40 hours  
**Automation Coverage**: 60%  
**Manual Coverage**: 40%  

**Status**: READY FOR QA TESTING ✅

---

## ✅ DEPLOYMENT READINESS CHECKLIST

- [ ] All 80 test cases pass
- [ ] Code coverage >= 80%
- [ ] Security tests pass (SQL injection, XSS, CSRF)
- [ ] Performance tests pass
- [ ] Responsive design verified
- [ ] Payment flow tested with live Stripe account (sandbox)
- [ ] Webhook signature verification enabled
- [ ] Rate limiting active
- [ ] Error monitoring configured
- [ ] Database backups tested

**QA Sign-Off**: ___________  
**Date**: ___________

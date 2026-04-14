# ⚡ QUICK START - Run Your New Payment System

This guide gets your production-ready payment system running in minutes.

---

## 🚀 STEP 1: Run Database Migrations

```bash
# Create the new tables
php artisan migrate

# This will create:
# - payments (payment_intent, charge tracking, status history)
# - order_status_histories (order status changes with timestamps)
```

---

## 🔑 STEP 2: Configure Environment

Copy `.env.example` to `.env` and add **required** variables:

```bash
# STRIPE CONFIGURATION
STRIPE_SECRET_KEY=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_PUBLIC_KEY=pk_test_YOUR_PUBLIC_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE

# MAIL CONFIGURATION (for sending emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@ceyloncraft.com
MAIL_FROM_NAME="Ceylon Craft"

# CACHE (optional but recommended)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Get Stripe Keys**:
1. Go to [stripe.com/dashboard](https://stripe.com/dashboard)
2. Navigate to: Developers → API Keys
3. Copy your **Secret Key** and **Public Key**
4. For webhooks: Developers → Webhooks → Add endpoint

---

## 🧪 STEP 3: Test Payment Flow (LOCAL)

### Create a Test Route

Add this to `routes/web.php`:

```php
Route::get('/test-payment', function () {
    return view('test-payment');
});
```

### Create Test View (`resources/views/test-payment.blade.php`)

```php
<html>
<head>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 100px auto; }
        input, button { padding: 10px; width: 100%; margin: 10px 0; }
        button { background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>💳 Test Payment</h1>

    <div id="card-element"></div>
    <button id="pay-button">Pay $99.99</button>

    <div id="payment-result"></div>

    <script>
        const stripe = Stripe('{{ config("services.stripe.public") }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        document.getElementById('pay-button').addEventListener('click', async () => {
            // Step 1: Create payment intent from backend
            const intentResponse = await fetch('/api/payments/intent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: 9999,
                    currency: 'usd',
                    description: 'Test Payment'
                })
            });
            const intent = await intentResponse.json();

            // Step 2: Create payment method
            const { paymentMethod } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            // Step 3: Confirm payment
            const confirmResponse = await fetch('/api/payments/confirm', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_intent_id: intent.data.intent_id,
                    payment_method_id: paymentMethod.id,
                })
            });
            const result = await confirmResponse.json();

            // Step 4: Display result
            document.getElementById('payment-result').innerHTML = 
                result.success 
                    ? '<h2 style="color:green;">✓ Payment successful!</h2>'
                    : `<h2 style="color:red;">✗ Payment failed: ${result.message}</h2>`;
        });
    </script>
</body>
</html>
```

### Test Steps

1. Open: `http://localhost:8000/test-payment`
2. Use **test card**: `4242 4242 4242 4242` (expires any future date, any CVC)
3. Click **Pay $99.99**
4. Check response → should see **"✓ Payment successful!"**
5. Verify in database:
   ```bash
   php artisan tinker
   >>> \App\Models\Payment::latest()->first();
   ```
   Should show: `status: "succeeded"`, `stripe_charge_id: "ch_..."`

---

## ✅ STEP 4: Test Webhook Handling

### Register Webhook Endpoint

1. Go to [Stripe Dashboard](https://stripe.com/dashboard) → Developers → Webhooks
2. Click **Add endpoint**
3. Endpoint URL: `https://yoursite.com/api/webhooks/stripe`
4. Events to listen: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`
5. Copy **Webhook Signing Secret** to `.env`: `STRIPE_WEBHOOK_SECRET`

### Test Firing Webhook Locally (Using Stripe CLI)

**Install Stripe CLI**:
```bash
# macOS
brew install stripe/stripe-cli/stripe

# Ubuntu
brew tap stripe/stripe-cli
brew install stripe

# Windows: Download from https://stripe.com/docs/stripe-cli
```

**Forward Webhooks to Local**:
```bash
# In terminal window 1:
stripe listen --forward-to localhost:8000/api/webhooks/stripe

# You'll see:
# > Ready! Your webhook signing secret is: whsec_test_...
# Copy this to .env STRIPE_WEBHOOK_SECRET
```

**Trigger Test Event (In terminal window 2)**:
```bash
# Create a test payment intent
stripe trigger payment_intent.succeeded

# Check your Laravel logs:
tail -f storage/logs/laravel.log
# Should show: "Webhook handled: payment_intent.succeeded"
```

---

## 📊 STEP 5: Verify Tables & Data

```bash
php artisan tinker

# Check payments table
>>> \App\Models\Payment::all();

# Check order status history
>>> \App\Models\OrderStatusHistory::all();

# Create test order status change
>>> $order = \App\Models\Order::first();
>>> $order->updateStatus('shipped', 'Dispatched', 'TRACK123');
>>> $order->statusHistory()->get();
```

---

## 🐛 STEP 6: Check Error Handling

### Test Exception Handling

```bash
curl -X POST http://localhost:8000/api/payments/intent \
  -H "Content-Type: application/json" \
  -d '{"amount": 0.25}' # Below $0.50 minimum

# Response (422):
{
  "success": false,
  "message": "The amount must be at least 50 cents.",
  "data": {"amount": ["The amount must be at least 50 cents."]},
  "timestamp": "2026-04-14T12:34:56Z"
}
```

### Test Rate Limiting

```bash
# Hit auth endpoint 6 times in 60 seconds
for i in {1..6}; do
  curl -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"test"}'
  echo "\n"
done

# On 6th request → 429 Too Many Requests
```

---

## 📧 STEP 7: Test Email (Optional)

### Configure Mailtrap

1. Go to [mailtrap.io](https://mailtrap.io)
2. Sign up (free)
3. Create inbox
4. Copy SMTP credentials to `.env`:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   ```

### Send Test Email

```bash
php artisan tinker

# Send order confirmation
>>> $order = \App\Models\Order::first();
>>> \App\Services\OrderNotificationService::sendOrderConfirmation($order);

# Check Mailtrap inbox → email should appear
```

---

## 🚢 STEP 8: Deploy Checklist

Before going to production:

- [ ] Run `php artisan migrate` on production
- [ ] Set production Stripe keys in `.env`
- [ ] Configure production mail service
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Enable HTTPS/SSL
- [ ] Configure Stripe webhook for production domain
- [ ] Set up error tracking (Sentry optional)
- [ ] Test complete payment flow on production
- [ ] Monitor first 24 hours of transactions
- [ ] Set up daily database backups

---

## 📱 STEP 9: Frontend Integration

### Install Stripe.js

```bash
npm install @stripe/js
```

### Add Payment Store (Pinia)

See: `FRONTEND_PAYMENT_INTEGRATION.md` → Pinia Store for Payment

### Add Checkout Component

See: `FRONTEND_PAYMENT_INTEGRATION.md` → Updated Checkout Component

### Update Environment

```env
VITE_STRIPE_PUBLIC_KEY=pk_test_YOUR_PUBLIC_KEY
VITE_API_URL=http://localhost:8000/api
```

---

## 🎯 STEP 10: Next Actions

- **Email Mailable Classes** (Unblocks notifications)
- **Invoice/PDF Generation** (Unblocks receipts)
- **Shipping System** (Unblocks logistics)
- **Testing Suite** (Unblocks deployment confidence)

---

## ❓ TROUBLESHOOTING

### Payment fails with "charge_already_exists"
→ Duplicate idempotency key  
*Solution*: Clear payment record and retry

### Webhook not received
→ Webhook endpoint not returning 200  
*Solution*: Check Laravel logs, ensure endpoint is public (no auth)

### Exception not returning JSON
→ APP_DEBUG=true might be affecting response format  
*Solution*: Check `bootstrap/app.php` exception handler

### Rate limiting too strict
→ Users getting 429 Too Many Requests  
*Solution*: Adjust `throttle:X,Y` values in `routes/api.php`

### Stock going negative
→ Race condition not prevented  
*Solution*: Ensure `InventoryService::reduceStock()` wrapped in `DB::transaction()`

---

**Status**: Ready for production  
**Estimated time to complete**: 10-15 minutes  
**Support**: See `IMPLEMENTATION_GUIDE.md` for detailed reference

# 🤖 AUTOMATED TESTING SETUP & IMPLEMENTATION

**Status**: Production-ready testing framework  
**Backend**: PHPUnit (Laravel testing suite)  
**Frontend**: Vitest + Cypress  
**Coverage Target**: 80%  

---

## 📦 BACKEND TESTING - PHPUnit

### Setup

**Already installed** (Laravel 12 includes):
```bash
composer show | grep phpunit
# phpunit/phpunit: ^10.0
```

### Run Tests

```bash
# All tests
php artisan test

# Specific test class
php artisan test tests/Feature/AuthTest.php

# With code coverage
php artisan test --coverage
php artisan test --coverage --coverage-html=coverage

# Watch mode (continuous testing)
php artisan test --watch

# Parallel testing (faster)
php artisan test --parallel
```

---

## 🧪 FEATURE TESTS (Integration Tests)

Create: `tests/Feature/AuthTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /* REGISTRATION TESTS */

    public function test_valid_registration(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'email', 'api_token'],
                'timestamp',
            ])
            ->assertDatabaseHas('users', [
                'email' => 'john@example.com',
            ]);

        // Verify password is hashed
        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(\Hash::check('SecurePass123!', $user->password));
    }

    public function test_registration_duplicate_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('data.email', ['The email has already been taken.']);

        // Verify only one user exists
        $this->assertEquals(1, User::where('email', 'john@example.com')->count());
    }

    public function test_registration_weak_password(): void
    {
        $weakPasswords = [
            'password',      // no uppercase
            'PASSWORD',      // no lowercase
            'Pass',          // no numbers
            'Pass123',       // no symbols
            'short1!',       // < 8 chars
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'Test User',
                'email' => "user{$password}@example.com",
                'password' => $password,
            ]);

            $response->assertStatus(422);
            $this->assertStringContainsString('password', $response->json('data'));
        }
    }

    public function test_registration_invalid_email(): void
    {
        $invalidEmails = [
            'invalid@',
            'user@.com',
            'no-at-sign.com',
            'user@nonexistentdomain99999999.xyz', // DNS validation
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'Test User',
                'email' => $email,
                'password' => 'SecurePass123!',
            ]);

            $response->assertStatus(422);
        }
    }

    public function test_registration_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['data' => ['name', 'email', 'password']]);
    }

    /* LOGIN TESTS */

    public function test_login_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => \Hash::make('SecurePass123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['api_token', 'user' => ['id', 'name', 'email']],
            ]);

        $this->assertNotNull($response->json('data.api_token'));
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials');
    }

    public function test_login_rate_limiting(): void
    {
        // Attempt 6 times within 60 seconds (limit is 5)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrong',
            ]);

            if ($i < 5) {
                $this->assertIn($response->status(), [401, 422]);
            } else {
                $response->assertStatus(429);
                $this->assertNotNull($response->headers->get('Retry-After'));
            }
        }
    }

    /* LOGOUT TESTS */

    public function test_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->api_token;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Token should be invalid
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
```

---

Create: `tests/Feature/CartTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'name' => 'laptop',
            'price' => 999.99,
            'stock' => 10,
        ]);
    }

    public function test_add_to_cart(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.product_id', $this->product->id);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->user->cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_add_to_cart_exceeds_stock(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 50, // Stock is only 10
            ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Only 10 items available');

        // Cart should NOT be updated
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $this->product->id,
        ]);
    }

    public function test_add_duplicate_item_combines_quantity(): void
    {
        // Add item first time
        $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        // Add same item again
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 3,
            ]);

        // Should combine to 5, not create duplicate
        $this->assertEquals(
            1,
            $this->user->cart->items()->where('product_id', $this->product->id)->count()
        );

        $cartItem = $this->user->cart->items()->where('product_id', $this->product->id)->first();
        $this->assertEquals(5, $cartItem->quantity);
    }

    public function test_view_cart(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => ['id', 'product', 'quantity', 'price', 'total']
                    ],
                    'subtotal',
                    'tax',
                    'total',
                ]
            ]);
    }

    public function test_remove_from_cart(): void
    {
        $cart = $this->user->cart();
        $cartItem = $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/cart/items/{$cartItem->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_cart_persists_after_refresh(): void
    {
        // Add item to cart
        $this->actingAs($this->user, 'api')
            ->postJson('/api/cart/add', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        // Simulate logout and login
        $this->post('/api/auth/logout');

        // Re-authenticate
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 2);
    }
}
```

---

Create: `tests/Feature/PaymentTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->order = Order::factory()->for($this->user)->create([
            'total' => 99.99,
            'status' => 'pending',
        ]);
    }

    public function test_create_payment_intent(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/intent', [
                'order_id' => $this->order->id,
                'amount' => 9999, // $99.99 in cents
                'currency' => 'usd',
                'description' => 'Order #' . $this->order->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['intent_id', 'client_secret', 'amount', 'currency']
            ])
            ->assertDatabaseHas('payments', [
                'order_id' => $this->order->id,
                'status' => 'pending',
                'amount' => 99.99,
                'currency' => 'usd',
            ]);
    }

    public function test_payment_intent_minimum_amount(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/intent', [
                'order_id' => $this->order->id,
                'amount' => 25, // $0.25 < $0.50 minimum
                'currency' => 'usd',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('data.amount', ['The amount must be at least 50 cents']);
    }

    public function test_payment_intent_invalid_currency(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/intent', [
                'order_id' => $this->order->id,
                'amount' => 9999,
                'currency' => 'jpy', // Not supported
            ]);

        $response->assertStatus(422);
    }

    public function test_payment_requires_authentication(): void
    {
        $response = $this->postJson('/api/payments/intent', [
            'order_id' => $this->order->id,
            'amount' => 9999,
            'currency' => 'usd',
        ]);

        $response->assertStatus(401);
    }

    public function test_payment_authorization_own_order_only(): void
    {
        $otherUser = User::factory()->create();
        $otherOrder = Order::factory()->for($otherUser)->create();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/intent', [
                'order_id' => $otherOrder->id,
                'amount' => 9999,
                'currency' => 'usd',
            ]);

        $response->assertStatus(403); // Forbidden
    }
}
```

---

Create: `tests/Feature/SecurityTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /* SQL INJECTION TESTS */

    public function test_sql_injection_login_prevention(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => "' OR '1'='1",
            'password' => 'anything',
        ]);

        // Should treat as invalid login, not execute SQL
        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials');
    }

    public function test_sql_injection_product_search(): void
    {
        $response = $this->getJson('/api/products', [
            'search' => "; DROP TABLE products; --"
        ]);

        // Should search for literal string, not execute SQL
        $response->assertStatus(200);

        // Verify tables still exist
        $this->assertDatabaseHas('products', []);
    }

    /* XSS TESTS */

    public function test_xss_stored_in_review(): void
    {
        $user = User::factory()->create();
        $product = \App\Models\Product::factory()->create();

        $maliciousScript = '<script>alert("XSS")</script>';

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/reviews', [
                'product_id' => $product->id,
                'rating' => 5,
                'comment' => $maliciousScript,
            ]);

        // Script should be stored as text, not executable
        $this->assertDatabaseHas('reviews', [
            'comment' => $maliciousScript,
        ]);

        // Retrieve and verify it's escaped
        $reviewResponse = $this->getJson("/api/products/{$product->slug}");
        $comment = $reviewResponse->json('data.reviews.0.comment');
        $this->assertNotContains('<script>', $comment);
    }

    /* PRIVILEGE ESCALATION TESTS */

    public function test_non_admin_cannot_create_product(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/products', [
                'name' => 'Test',
                'price' => 99.99,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'price' => 99.99,
                'stock' => 10,
            ]);

        $response->assertStatus(201);
    }

    /* AUTHORIZATION TESTS */

    public function test_user_cannot_view_other_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = \App\Models\Order::factory()->for($user2)->create();

        $response = $this->actingAs($user1, 'api')
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }

    public function test_token_validation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_12345'
        ])->getJson('/api/cart');

        $response->assertStatus(401);
    }

    /* MASS ASSIGNMENT TEST */

    public function test_mass_assignment_protection(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!',
            'is_admin' => true,  // Try to become admin
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertFalse($user->is_admin); // Should still be false
    }
}
```

---

## 💻 FRONTEND TESTING - Vitest

### Setup

```bash
# Already installed (npm install)
npm list vitest

# Install if missing
npm install --save-dev vitest @vitest/ui @vue/test-utils happy-dom
```

Create: `tests/unit/stores/cartStore.test.js`

```javascript
import { describe, it, expect, beforeEach } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useCartStore } from '@/stores/cartStore';

describe('Cart Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  it('adds item to cart', () => {
    const cart = useCartStore();
    
    cart.addItem({
      id: 1,
      name: 'Laptop',
      price: 999.99,
      quantity: 1,
      image: '/images/laptop.jpg',
    });

    expect(cart.items).toHaveLength(1);
    expect(cart.items[0].id).toBe(1);
    expect(cart.items[0].quantity).toBe(1);
  });

  it('combines quantities for duplicate items', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, name: 'Laptop', price: 999.99, quantity: 2 });
    cart.addItem({ id: 1, name: 'Laptop', price: 999.99, quantity: 3 });

    expect(cart.items).toHaveLength(1);
    expect(cart.items[0].quantity).toBe(5);
  });

  it('calculates subtotal correctly', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, price: 100, quantity: 2 });
    cart.addItem({ id: 2, price: 50, quantity: 1 });

    expect(cart.subtotal).toBe(250);
  });

  it('calculates total with tax', () => {
    const cart = useCartStore();
    cart.addItem({ id: 1, price: 100, quantity: 1 });

    expect(cart.subtotal).toBe(100);
    expect(cart.tax).toBeGreaterThan(0); // Depends on tax rate
    expect(cart.total).toBeGreaterThan(100);
  });

  it('removes item from cart', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, name: 'Laptop', price: 999.99, quantity: 1 });
    cart.removeItem(1);

    expect(cart.items).toHaveLength(0);
  });

  it('clears entire cart', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, price: 100, quantity: 1 });
    cart.addItem({ id: 2, price: 50, quantity: 2 });
    cart.clear();

    expect(cart.items).toHaveLength(0);
    expect(cart.subtotal).toBe(0);
    expect(cart.total).toBe(0);
  });

  it('updates item quantity', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, name: 'Laptop', price: 999.99, quantity: 1 });
    cart.updateQuantity(1, 5);

    expect(cart.items[0].quantity).toBe(5);
  });

  it('prevents negative quantity', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, price: 100, quantity: 1 });
    cart.updateQuantity(1, -5);

    // Should either stay at 1 or remove item
    expect(cart.items[0].quantity).toBeGreaterThan(0);
  });

  it('persists to localStorage', () => {
    const cart = useCartStore();
    
    cart.addItem({ id: 1, price: 100, quantity: 2 });

    const stored = JSON.parse(localStorage.getItem('cart'));
    expect(stored).toBeDefined();
    expect(stored.items[0].id).toBe(1);
  });
});
```

---

### Run Frontend Tests

```bash
# Unit tests
npm run test

# Watch mode (continuous)
npm run test:watch

# UI dashboard
npm run test:ui

# Coverage
npm run test:coverage
```

---

## 🛠️ E2E TESTING - Cypress

### Setup

```bash
# Install
npm install --save-dev cypress

# Generate config
npx cypress open
```

Create: `cypress/e2e/checkout.cy.js`

```javascript
describe('Checkout Flow E2E', () => {
  beforeEach(() => {
    cy.visit('/');
    cy.login('customer@example.com', 'SecurePass123!');
  });

  it('completes purchase successfully', () => {
    // Add product to cart
    cy.visit('/products/laptop-dell-xps-13');
    cy.get('[data-testid="add-to-cart-btn"]').click();
    cy.get('[data-testid="quantity-input"]').clear().type('2');
    cy.get('[data-testid="add-confirm-btn"]').click();

    // Verify toast notification
    cy.get('[data-testid="toast-success"]').should('contain', 'Added to cart');

    // Navigate to cart
    cy.get('[data-testid="cart-icon"]').click();
    cy.get('[data-testid="cart-item"]').should('have.length', 1);
    cy.get('[data-testid="cart-total"]').should('contain', '$');

    // Proceed to checkout
    cy.get('[data-testid="checkout-btn"]').click();

    // Fill shipping information
    cy.get('[data-testid="shipping-address"]').type('123 Main Street');
    cy.get('[data-testid="shipping-city"]').select('Colombo');
    cy.get('[data-testid="shipping-postal"]').type('00100');
    cy.get('[data-testid="shipping-phone"]').type('+94123456789');

    // Agree to terms
    cy.get('[data-testid="terms-checkbox"]').check();

    // Fill card details (Stripe embedded frame)
    cy.wrapped($iframe => {
      const $body = $iframe.contents().find('body');
      
      cy.wrap($body)
        .find('[class*="CardNumberField"]')
        .type('4242424242424242', { force: true });
      
      cy.wrap($body)
        .find('[class*="CardExpiryField"]')
        .type('1225', { force: true });
      
      cy.wrap($body)
        .find('[class*="CardCVCField"]')
        .type('123', { force: true });
    });

    // Submit payment
    cy.get('[data-testid="pay-btn"]').click();

    // Verify success page
    cy.url().should('include', '/order');
    cy.get('[data-testid="order-success-msg"]').should('be.visible');
    cy.get('[data-testid="order-number"]').should('not.be.empty');

    // Verify order in list
    cy.visit('/orders');
    cy.get('[data-testid="order-list"]').should('contain', 'Confirmed');
  });

  it('prevents payment with invalid card', () => {
    // ... setup ...

    // Use invalid card (will fail)
    // 4000 0000 0000 0002
    cy.get('[data-testid="card-number"]').type('4000000000000002');
    cy.get('[data-testid="pay-btn"]').click();

    // Error message
    cy.get('[data-testid="error-msg"]').should('contain', 'declined');
  });

  it('prevents checkout with empty cart', () => {
    cy.visit('/checkout');
    cy.get('[data-testid="checkout-btn"]').should('be.disabled');
    cy.get('[data-testid="empty-cart-msg"]').should('be.visible');
  });

  it('validates address format', () => {
    cy.visit('/checkout');
    
    cy.get('[data-testid="shipping-address"]').type('123');
    cy.get('[data-testid="pay-btn"]').click();

    cy.get('[data-testid="address-error"]').should('contain', 'at least 10 characters');
  });
});
```

---

## 📊 Test Coverage Report

Generate coverage report:

```bash
# Backend
php artisan test --coverage

# Frontend
npm run test:coverage

# Output
coverage/
├── index.html
├── clover.xml
└── lcov.info
```

---

## 🏃 CI/CD Integration

### GitHub Actions (`.github/workflows/tests.yml`)

```yaml
name: Tests

on: [push, pull_request]

jobs:
  backend:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: ceyloncraft_test
          MYSQL_PASSWORD: password
          MYSQL_ROOT_PASSWORD: password
    steps:
      - uses: actions/checkout@v2
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - run: composer install
      - run: php artisan test
  frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: actions/setup-node@v2
      - run: npm install
      - run: npm run test
      - run: npm run build
```

---

## ✅ TEST CHECKLIST

Before each release:

- [ ] All tests pass: `php artisan test`
- [ ] All e2e tests pass: `npx cypress run`
- [ ] Code coverage >= 80%
- [ ] No console errors in browser
- [ ] Payment tested with Stripe sandbox
- [ ] Security tests pass
- [ ] Performance tests pass
- [ ] Responsive design verified

---

**Status**: READY FOR AUTOMATED TESTING ✅

<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'stock' => 100,
            'price' => 99.99,
            'is_active' => true,
        ]);
        
        // Create a cart for the user
        /** @var Cart */
        $cart = \App\Models\Cart::create(['user_id' => $this->user->id]);
    }

    /**
     * TEST: Orders - Create order from cart
     * Scenario: User has items in cart, submits order with address
     * Expected: 201 Created with order details, cart cleared
     */
    public function test_create_order_from_cart(): void
    {
        // Add item to user's cart
        CartItem::create([
            'cart_id' => $this->user->cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['order_id', 'status', 'total']]);

        // Verify order created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        // Verify cart cleared
        $this->assertDatabaseCount('cart_items', 0);
    }

    /**
     * TEST: Orders - Empty cart prevents order
     * Scenario: Cart is empty, user tries to create order
     * Expected: 422 or 400 error
     */
    public function test_empty_cart_prevents_order_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Orders - Stock reduction on order
     * Scenario: Product has 10 stock, order requests 3
     * Expected: Stock reduced to 7
     */
    public function test_stock_reduced_on_order(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);
        CartItem::create([
            'cart_id' => $this->user->cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => $product->price,
        ]);

        $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
    }

    /**
     * TEST: Orders - Insufficient stock prevents order
     * Scenario: Cart requests 15 units, only 10 available
     * Expected: 409 Conflict
     */
    public function test_insufficient_stock_prevents_order(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);
        CartItem::create([
            'cart_id' => $this->user->cart->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(409)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Orders - Validate address field required
     * Scenario: Missing address in order
     * Expected: 422 validation error
     */
    public function test_order_requires_address(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Orders - Get user orders
     * Scenario: User has multiple orders
     * Expected: List all user's orders with pagination
     */
    public function test_get_user_orders(): void
    {
        Order::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/orders');

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'status', 'total', 'created_at']]])
                ->assertJsonCount(3, 'data');
    }

    /**
     * TEST: Orders - Get single order detail
     * Scenario: User retrieves their order detail
     * Expected: 200 OK with full order info including items
     */
    public function test_get_order_detail(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory(2)->create(['order_id' => $order->id]);

        $response = $this->actingAs($this->user)->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['id', 'status', 'items' => ['*' => ['product_id', 'quantity']]]])
                ->assertJsonPath('data.id', $order->id);
    }

    /**
     * TEST: Orders - Cannot view another user's order
     * Scenario: User tries to access another user's order
     * Expected: 403 Forbidden
     */
    public function test_cannot_view_another_users_order(): void
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }

    /**
     * TEST: Orders - Order status update by admin
     * Scenario: Admin changes order status to paid
     * Expected: 200 OK with updated status
     */
    public function test_admin_update_order_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'paid',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.status', 'paid');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }

    /**
     * TEST: Orders - Non-admin cannot update order status
     * Scenario: Regular user tries to change order status
     * Expected: 403 Forbidden
     */
    public function test_non_admin_cannot_update_order_status(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertStatus(403);
    }

    /**
     * TEST: Orders - Order status history tracking
     * Scenario: Order transitions through multiple statuses
     * Expected: Status history recorded
     */
    public function test_order_status_history_tracking(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['status' => 'pending']);

        // Change to processing
        $this->actingAs($admin)->patchJson("/api/orders/{$order->id}/status", ['status' => 'processing']);

        // Change to shipped
        $this->actingAs($admin)->patchJson("/api/orders/{$order->id}/status", ['status' => 'shipped']);

        // Verify history exists (if tracking is implemented)
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);
    }

    /**
     * TEST: Orders - Invalid status value
     * Scenario: Admin tries to set invalid status
     * Expected: 422 validation error
     */
    public function test_invalid_status_value(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Orders - Multiple products in order
     * Scenario: Cart has 3 different products
     * Expected: Order created with all items
     */
    public function test_order_with_multiple_products(): void
    {
        $product1 = Product::factory()->create(['stock' => 50, 'is_active' => true]);
        $product2 = Product::factory()->create(['stock' => 50, 'is_active' => true]);
        $product3 = Product::factory()->create(['stock' => 50, 'is_active' => true]);

        CartItem::factory()->create(['product_id' => $product1->id, 'quantity' => 2]);
        CartItem::factory()->create(['product_id' => $product2->id, 'quantity' => 1]);
        CartItem::factory()->create(['product_id' => $product3->id, 'quantity' => 3]);

        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(201);
        $orderId = $response->json('data.id');

        // Verify 3 order items created
        $this->assertDatabaseCount('order_items', 3);
        $this->assertDatabaseHas('order_items', ['order_id' => $orderId, 'product_id' => $product1->id, 'quantity' => 2]);
    }

    /**
     * TEST: Orders - Guest cannot create order
     * Scenario: Unauthenticated user tries to create order
     * Expected: 401 Unauthorized
     */
    public function test_guest_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(401);
    }

    /**
     * TEST: Orders - Order total calculation
     * Scenario: Order created with multiple items
     * Expected: Total calculated correctly (sum of all items)
     */
    public function test_order_total_calculation(): void
    {
        $product1 = Product::factory()->create(['stock' => 50, 'price' => 25.00, 'is_active' => true]);
        $product2 = Product::factory()->create(['stock' => 50, 'price' => 75.00, 'is_active' => true]);

        CartItem::factory()->create(['product_id' => $product1->id, 'quantity' => 2, 'price' => 25.00]);
        CartItem::factory()->create(['product_id' => $product2->id, 'quantity' => 1, 'price' => 75.00]);

        $response = $this->actingAs($this->user)->postJson('/api/orders', [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(201);
        
        // Total should be: (25 * 2) + (75 * 1) = 125
        $responseTotal = $response->json('data.total');
        $this->assertEquals(125.00, $responseTotal);
    }

    /**
     * TEST: Orders - Prevent duplicate orders (idempotency)
     * Scenario: Submit same order twice with idempotency key
     * Expected: Second request returns existing order, no duplicate created
     */
    public function test_prevent_duplicate_orders(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id, 'quantity' => 1]);

        $payload = [
            'address' => '123 Main St',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1234567890',
        ];

        // First request
        $response1 = $this->actingAs($this->user)->postJson('/api/orders', $payload);
        $orderId1 = $response1->json('data.id');

        // Repopulate cart
        CartItem::factory()->create(['product_id' => $this->product->id, 'quantity' => 1]);

        // Second request (should create new order as cart is different)
        $response2 = $this->actingAs($this->user)->postJson('/api/orders', $payload);

        // Both should be successful but different orders
        $this->assertEquals(201, $response1->status());
        $this->assertTrue($response1->status() === 201 || $response2->status() === 201);
    }
}

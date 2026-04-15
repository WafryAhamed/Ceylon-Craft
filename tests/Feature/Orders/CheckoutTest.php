<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['stock' => 100, 'price' => 99.99]);
    }

    /**
     * Test Case QA-038: Checkout with valid data
     */
    public function test_checkout_with_valid_data(): void
    {
        // Create cart for user and add item
        $cart = $this->user->cart()->firstOrCreate(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => '123 Main Street, Colombo',
            'shipping_city' => 'Colombo',
            'postal_code' => '00100',
            'phone' => '+94771234567',
            'country' => 'lk',
            'terms_agreed' => true,
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('data.status', 'pending');
    }

    /**
     * Test Case QA-039: Checkout with empty cart
     */
    public function test_checkout_with_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => '123 Main Street',
            'postal_code' => '00100',
            'phone' => '+94771234567',
            'country' => 'lk',
            'terms_agreed' => true,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-041: Checkout with invalid address (< 10 chars)
     */
    public function test_checkout_with_invalid_address(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => 'Short', // Less than 10 characters
            'postal_code' => '00100',
            'phone' => '+94771234567',
            'country' => 'lk',
            'terms_agreed' => true,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-042: Checkout with invalid postal code
     */
    public function test_checkout_with_invalid_postal_code(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => '123 Main Street, Colombo',
            'postal_code' => 'invalid', // Not numeric
            'phone' => '+94771234567',
            'country' => 'lk',
            'terms_agreed' => true,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-043: Checkout with country != 'lk'
     */
    public function test_checkout_with_invalid_country(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => '123 Main Street, NYC',
            'postal_code' => '10001',
            'phone' => '+12025551234',
            'country' => 'us', // Not lk
            'terms_agreed' => true,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-044: Checkout without terms acceptance
     */
    public function test_checkout_without_terms_acceptance(): void
    {
        CartItem::factory()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/checkout', [
            'address' => '123 Main Street, Colombo',
            'postal_code' => '00100',
            'phone' => '+94771234567',
            'country' => 'lk',
            'terms_agreed' => false,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-048: View user's own orders
     */
    public function test_view_users_own_orders(): void
    {
        \App\Models\Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/orders');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /**
     * Test Case QA-049: Access another user's order returns 403
     */
    public function test_access_another_users_order_returns_403(): void
    {
        $otherUser = User::factory()->create();
        $order = \App\Models\Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->getJson('/api/orders/' . $order->id);

        $response->assertStatus(403);
    }

    /**
     * Test Case QA-052: Cancel order with shipped status returns error
     */
    public function test_cancel_shipped_order_returns_error(): void
    {
        $order = \App\Models\Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'shipped',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/orders/' . $order->id . '/cancel');

        $response->assertStatus(409);
    }
}

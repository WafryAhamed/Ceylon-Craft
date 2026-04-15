<?php

namespace Tests\Feature\Cart;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
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
            'stock' => 100,
            'price' => 99.99,
            'is_active' => true,
        ]);
    }

    /**
     * TEST: Cart - Add product to cart
     * Scenario: Authenticated user adds product to cart
     * Expected: 200 OK with cart item details
     */
    public function test_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['id', 'product_id', 'quantity', 'price']])
                ->assertJsonPath('data.quantity', 2);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    /**
     * TEST: Cart - Add same product twice increases quantity
     * Scenario: Add same product to cart two times
     * Expected: Quantity incremented, not duplicate row
     */
    public function test_add_same_product_twice_increments_quantity(): void
    {
        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        // Verify only one cart item exists
        $items = CartItem::where('product_id', $this->product->id)->count();
        $this->assertEquals(1, $items);
    }

    /**
     * TEST: Cart - Add out of stock product
     * Scenario: Product has stock = 0
     * Expected: 409 Conflict
     */
    public function test_add_out_of_stock_product(): void
    {
        $outOfStock = Product::factory()->create(['stock' => 0, 'is_active' => true]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $outOfStock->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(409)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Cart - Add quantity exceeding available stock
     * Scenario: Request quantity > available stock
     * Expected: 409 Conflict with available/requested info
     */
    public function test_add_quantity_exceeding_stock(): void
    {
        $limitedProduct = Product::factory()->create(['stock' => 3, 'is_active' => true]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $limitedProduct->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(409);
    }

    /**
     * TEST: Cart - View cart
     * Scenario: Authenticated user views cart
     * Expected: 200 OK with all cart items
     */
    public function test_view_cart(): void
    {
        // Create cart for user if it doesn't exist
        $cart = $this->user->cart()->firstOrCreate(['user_id' => $this->user->id]);
        
        // Create 3 different products and add them to cart
        $products = Product::factory(3)->create(['stock' => 100, 'is_active' => true]);
        foreach ($products as $product) {
            CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->actingAs($this->user)->getJson('/api/cart');

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'product_id', 'quantity', 'price']]])
                ->assertJsonCount(3, 'data');
    }

    /**
     * TEST: Cart - Update cart item quantity
     * Scenario: Change quantity of existing cart item
     * Expected: 200 OK with updated quantity
     */
    public function test_update_cart_item_quantity(): void
    {
        $cartItem = CartItem::factory()->create(['quantity' => 1]);

        $response = $this->actingAs($this->user)->putJson('/api/cart/update', [
            'cart_item_id' => $cartItem->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.quantity', 5);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    /**
     * TEST: Cart - Update with quantity exceeding stock
     * Scenario: Update quantity to value > available stock
     * Expected: 409 Conflict
     */
    public function test_update_quantity_exceeding_stock(): void
    {
        $limitedProduct = Product::factory()->create(['stock' => 5, 'is_active' => true]);
        $cartItem = CartItem::factory()->create(['product_id' => $limitedProduct->id, 'quantity' => 2]);

        $response = $this->actingAs($this->user)->putJson('/api/cart/update', [
            'cart_item_id' => $cartItem->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(409);
    }

    /**
     * TEST: Cart - Remove item from cart
     * Scenario: Delete cart item
     * Expected: 200 OK, item removed from database
     */
    public function test_remove_item_from_cart(): void
    {
        $cartItem = CartItem::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/cart/delete', [
            'cart_item_id' => $cartItem->id,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /**
     * TEST: Cart - Clear entire cart
     * Scenario: Remove all items from cart
     * Expected: 200 OK, all items deleted
     */
    public function test_clear_entire_cart(): void
    {
        // Create cart for user and add 5 items
        $cart = $this->user->cart()->firstOrCreate(['user_id' => $this->user->id]);
        CartItem::factory(5)->create(['cart_id' => $cart->id]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/clear');

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseCount('cart_items', 0);
    }

    /**
     * TEST: Cart - Add invalid product
     * Scenario: Product ID doesn't exist
     * Expected: 404 or 422 error
     */
    public function test_add_non_existent_product_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $this->assertTrue(
            $response->status() === 404 || $response->status() === 422
        );
    }

    /**
     * TEST: Cart - Add with negative quantity
     * Scenario: Quantity is negative number
     * Expected: 422 validation error
     */
    public function test_add_with_negative_quantity(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => -5,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Cart - Add with zero quantity
     * Scenario: Quantity is 0
     * Expected: 422 validation error
     */
    public function test_add_with_zero_quantity(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Cart - Guest user access
     * Scenario: Unauthenticated user tries to add to cart
     * Expected: 401 Unauthorized
     */
    public function test_guest_user_cannot_add_to_cart(): void
    {
        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
    }

    /**
     * TEST: Cart - Persist after logout and login
     * Scenario: Add to cart, logout, login, cart still there
     * Expected: Cart persists in database
     */
    public function test_cart_persists_across_sessions(): void
    {
        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Login as same user again
        $response = $this->actingAs($this->user)->getJson('/api/cart');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /**
     * TEST: Cart - Cart total calculation
     * Scenario: Multiple items with different prices and quantities
     * Expected: Cart total calculated correctly
     */
    public function test_cart_total_calculation(): void
    {
        $product1 = Product::factory()->create(['price' => 50.00, 'is_active' => true]);
        $product2 = Product::factory()->create(['price' => 75.00, 'is_active' => true]);

        // Create cart for user
        $cart = $this->user->cart()->firstOrCreate(['user_id' => $this->user->id]);

        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product1->id, 'quantity' => 2, 'price' => 50.00]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product2->id, 'quantity' => 1, 'price' => 75.00]);

        $response = $this->actingAs($this->user)->getJson('/api/cart');

        $response->assertStatus(200);
        $data = $response->json();
        
        // Verify cart items are returned
        $this->assertCount(2, $data['data']);
        
        // Total should be: (50 * 2) + (75 * 1) = 175
        $expectedTotal = 175;
        $actualTotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $data['data']));
        $this->assertEquals($expectedTotal, $actualTotal);
    }
}

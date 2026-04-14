<?php

namespace Tests\Feature\Cart;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartOperationsTest extends TestCase
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
     * Test Case QA-028: Add product to cart
     */
    public function test_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.product_id', $this->product->id)
                ->assertJsonPath('data.quantity', 1);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);
    }

    /**
     * Test Case QA-029: Add same product twice increases quantity
     */
    public function test_add_same_product_twice_increases_quantity(): void
    {
        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test Case QA-030: Add out-of-stock product returns error
     */
    public function test_add_out_of_stock_product_returns_error(): void
    {
        $outOfStockProduct = Product::factory()->create(['stock' => 0]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $outOfStockProduct->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(409); // Conflict
    }

    /**
     * Test Case QA-031: Add quantity exceeding stock returns error
     */
    public function test_add_quantity_exceeding_stock_returns_error(): void
    {
        $limitedProduct = Product::factory()->create(['stock' => 3]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $limitedProduct->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(409);
    }

    /**
     * Test Case QA-032: Update cart item quantity
     */
    public function test_update_cart_item_quantity(): void
    {
        $cartItem = CartItem::factory()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->putJson('/api/cart/update', [
            'cart_item_id' => $cartItem->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    /**
     * Test Case QA-033: Update quantity to 0 removes item
     */
    public function test_update_quantity_to_zero_removes_item(): void
    {
        $cartItem = CartItem::factory()->create();

        $this->actingAs($this->user)->putJson('/api/cart/update', [
            'cart_item_id' => $cartItem->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /**
     * Test Case QA-034: Remove item from cart
     */
    public function test_remove_item_from_cart(): void
    {
        $cartItem = CartItem::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/cart/remove', [
            'cart_item_id' => $cartItem->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /**
     * Test Case QA-037: Add with invalid quantity (negative)
     */
    public function test_add_with_negative_quantity_returns_error(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => -5,
        ]);

        $response->assertStatus(422);
    }
}

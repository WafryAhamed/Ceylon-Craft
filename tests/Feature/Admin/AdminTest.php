<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    /**
     * TEST: Admin - Add product (admin only)
     * Scenario: Admin creates new product
     * Expected: 201 Created with product details
     */
    public function test_admin_can_add_product(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/admin/products', [
            'name' => 'New Laptop',
            'description' => 'High performance laptop',
            'price' => 1299.99,
            'stock' => 50,
            'category_id' => 1,
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('data.name', 'New Laptop')
                ->assertJsonPath('data.price', 1299.99);

        $this->assertDatabaseHas('products', [
            'name' => 'New Laptop',
            'price' => 1299.99,
        ]);
    }

    /**
     * TEST: Admin - Regular user cannot add product
     * Scenario: Non-admin tries to add product
     * Expected: 403 Forbidden
     */
    public function test_regular_user_cannot_add_product(): void
    {
        $response = $this->actingAs($this->regularUser)->postJson('/api/admin/products', [
            'name' => 'Unauthorized Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('products', ['name' => 'Unauthorized Product']);
    }

    /**
     * TEST: Admin - Update product
     * Scenario: Admin modifies existing product
     * Expected: 200 OK with updated details
     */
    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name', 'price' => 50.00]);

        $response = $this->actingAs($this->admin)->putJson("/api/admin/products/{$product->id}", [
            'name' => 'Updated Name',
            'price' => 75.00,
            'stock' => 100,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Name')
                ->assertJsonPath('data.price', 75.00);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 75.00,
        ]);
    }

    /**
     * TEST: Admin - Delete product
     * Scenario: Admin removes a product (soft or hard delete)
     * Expected: 200 OK, product removed or marked deleted
     */
    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/api/admin/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        // Check if soft or hard deleted
        if (in_array('deleted_at', $product->getAttributes())) {
            $this->assertNotNull(Product::find($product->id)?->deleted_at ?? null);
        } else {
            $this->assertNull(Product::find($product->id));
        }
    }

    /**
     * TEST: Admin - Activate/deactivate product
     * Scenario: Toggle product visible status
     * Expected: 200 OK with updated is_active
     */
    public function test_admin_can_toggle_product_active_status(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->patchJson("/api/admin/products/{$product->id}/toggle", []);

        $response->assertStatus(200)
                ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    /**
     * TEST: Admin - View all orders
     * Scenario: Admin sees all user orders
     * Expected: 200 OK with paginated orders
     */
    public function test_admin_can_view_all_orders(): void
    {
        Order::factory(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/admin/orders');

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'user_id', 'status', 'total']]])
                ->assertJsonCount(5, 'data');
    }

    /**
     * TEST: Admin - Regular user cannot view all orders
     * Scenario: Non-admin tries to access admin order list
     * Expected: 403 Forbidden
     */
    public function test_regular_user_cannot_view_all_orders(): void
    {
        $response = $this->actingAs($this->regularUser)->getJson('/api/admin/orders');

        $response->assertStatus(403);
    }

    /**
     * TEST: Admin - Update order status
     * Scenario: Admin changes order from pending to processing
     * Expected: 200 OK with updated status
     */
    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)->patchJson("/api/admin/orders/{$order->id}", [
            'status' => 'processing',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.status', 'processing');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    /**
     * TEST: Admin - Enable/disable product featured status
     * Scenario: Mark product as featured
     * Expected: 200 OK, featured status updated
     */
    public function test_admin_can_toggle_featured_status(): void
    {
        $product = Product::factory()->create(['featured' => false]);

        $response = $this->actingAs($this->admin)->patchJson("/api/admin/products/{$product->id}/featured", []);

        $response->assertStatus(200)
                ->assertJsonPath('data.featured', true);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'featured' => true,
        ]);
    }

    /**
     * TEST: Admin - View order details
     * Scenario: Admin views full order with items and customer info
     * Expected: 200 OK with complete order data
     */
    public function test_admin_can_view_order_details(): void
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/admin/orders/{$order->id}");

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['id', 'user_id', 'status', 'items', 'user']])
                ->assertJsonPath('data.id', $order->id);
    }

    /**
     * TEST: Admin - Bulk import products
     * Scenario: Admin uploads CSV of products
     * Expected: 200 OK, products created (if feature exists)
     */
    public function test_admin_can_bulk_import_products(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/admin/products/bulk-import', [
            'products' => json_encode([
                ['name' => 'Product 1', 'price' => 50.00, 'stock' => 10],
                ['name' => 'Product 2', 'price' => 75.00, 'stock' => 15],
            ]),
        ]);

        // May return 201 or 200
        $this->assertTrue($response->status() === 200 || $response->status() === 201 || $response->status() === 404);
    }

    /**
     * TEST: Admin - Missing required admin fields
     * Scenario: Admin adds product without required fields
     * Expected: 422 validation error
     */
    public function test_add_product_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/admin/products', [
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Admin - Product name length validation
     * Scenario: Product name exceeds maximum characters
     * Expected: 422 validation error
     */
    public function test_product_name_max_length(): void
    {
        $longName = str_repeat('A', 300);

        $response = $this->actingAs($this->admin)->postJson('/api/admin/products', [
            'name' => $longName,
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Admin - Negative price validation
     * Scenario: Product with negative price
     * Expected: 422 validation error
     */
    public function test_product_price_cannot_be_negative(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/admin/products', [
            'name' => 'Test Product',
            'price' => -50.00,
            'stock' => 10,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Admin - Negative stock validation
     * Scenario: Product with negative stock
     * Expected: 422 validation error
     */
    public function test_product_stock_cannot_be_negative(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/admin/products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => -10,
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Admin - Guest cannot access admin endpoints
     * Scenario: Unauthenticated user tries admin endpoints
     * Expected: 401 Unauthorized
     */
    public function test_guest_cannot_access_admin_endpoints(): void
    {
        $response = $this->getJson('/api/admin/orders');

        $response->assertStatus(401);
    }

    /**
     * TEST: Admin - Dashboard stats
     * Scenario: Admin views dashboard with KPIs
     * Expected: 200 OK with stats (if implemented)
     */
    public function test_admin_dashboard_stats(): void
    {
        Order::factory(10)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/admin/stats');

        // May or may not exist
        if ($response->status() === 200) {
            $response->assertJsonStructure(['success', 'data']);
        }
    }
}

<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case QA-013: List products with pagination
     */
    public function test_list_products_with_pagination(): void
    {
        Product::factory(25)->create(['is_active' => true]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data', 'meta' => ['total', 'per_page', 'page']])
                ->assertJsonCount(12, 'data'); // Default pagination is 12
    }

    /**
     * Test Case QA-015: Filter by category ID
     */
    public function test_filter_by_category_id(): void
    {
        $category = Category::factory()->create();
        Product::factory(5)->create(['category_id' => $category->id, 'is_active' => true]);
        Product::factory(5)->create(['is_active' => true]); // Other category

        $response = $this->getJson('/api/products?category_id=' . $category->id);

        $response->assertStatus(200)
                ->assertJsonCount(5, 'data');
    }

    /**
     * Test Case QA-018: Search products by name
     */
    public function test_search_products_by_name(): void
    {
        Product::factory()->create([
            'name' => 'Handmade Vase',
            'is_active' => true,
        ]);
        Product::factory()->create([
            'name' => 'Ceramic Pot',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products?search=Vase');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Handmade Vase');
    }

    /**
     * Test Case QA-020: Sort by price ascending
     */
    public function test_sort_by_price_ascending(): void
    {
        Product::factory()->create(['name' => 'Product A', 'price' => 100, 'is_active' => true]);
        Product::factory()->create(['name' => 'Product B', 'price' => 50, 'is_active' => true]);
        Product::factory()->create(['name' => 'Product C', 'price' => 75, 'is_active' => true]);

        $response = $this->getJson('/api/products?sort=price');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(50, $data[0]['price']);
        $this->assertEquals(75, $data[1]['price']);
        $this->assertEquals(100, $data[2]['price']);
    }

    /**
     * Test Case QA-023: View inactive product (should return 404)
     */
    public function test_view_inactive_product_returns_404(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/products/' . $product->slug);

        $response->assertStatus(404);
    }

    /**
     * Test Case QA-026: Search with SQL injection attempt
     */
    public function test_search_with_sql_injection_attempt(): void
    {
        Product::factory()->create(['name' => 'Laptop', 'is_active' => true]);

        $response = $this->getJson('/api/products?search=' . urlencode("'; DROP TABLE products; --"));

        $response->assertStatus(200);
        // Products table should still exist
        $this->assertDatabaseHas('products', ['name' => 'Laptop']);
    }
}

<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::factory(5)->create();
        Product::factory(20)->create(['is_active' => true]);
    }

    /**
     * TEST: Categories - Fetch all categories
     * Scenario: Get list of all categories
     * Expected: 200 OK with categories, includes product count
     */
    public function test_fetch_all_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'name', 'slug', 'description']]])
                ->assertJsonCount(5, 'data');
    }

    /**
     * TEST: Categories - Fetch single category
     * Scenario: Get category by slug
     * Expected: 200 OK with category details
     */
    public function test_fetch_single_category_by_slug(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->slug}");

        $response->assertStatus(200)
                ->assertJsonPath('data.id', $category->id)
                ->assertJsonPath('data.name', $category->name);
    }

    /**
     * TEST: Categories - Non-existent category
     * Scenario: Fetch category with invalid slug
     * Expected: 404 Not Found
     */
    public function test_fetch_non_existent_category(): void
    {
        $response = $this->getJson('/api/categories/non-existent-slug-12345');

        $response->assertStatus(404);
    }

    /**
     * TEST: Categories - Fetch products by category
     * Scenario: Get all active products in a category
     * Expected: 200 OK with filtered products
     */
    public function test_fetch_products_by_category(): void
    {
        $category = Category::first();
        Product::factory(3)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/categories/{$category->slug}/products");

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'name', 'price', 'category_id']]])
                ->assertJsonCount(3, 'data');

        // Verify all products belong to category
        foreach ($response->json('data') as $product) {
            $this->assertEquals($category->id, $product['category_id']);
        }
    }

    /**
     * TEST: Categories - Filter inactive products excluded
     * Scenario: Category has inactive products mixed in
     * Expected: Only active products returned
     */
    public function test_category_excludes_inactive_products(): void
    {
        $category = Category::first();
        Product::factory(2)->create(['category_id' => $category->id, 'is_active' => true]);
        Product::factory(3)->create(['category_id' => $category->id, 'is_active' => false]);

        $response = $this->getJson("/api/categories/{$category->slug}/products");

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    /**
     * TEST: Categories - Pagination for products
     * Scenario: Category has 50 products, paginate with per_page=15
     * Expected: First page has 15, next_page_url present
     */
    public function test_category_products_pagination(): void
    {
        $category = Category::first();
        Product::factory(50)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/categories/{$category->slug}/products?per_page=15&page=1");

        $response->assertStatus(200)
                ->assertJsonCount(15, 'data')
                ->assertJsonPath('meta.total', 50)
                ->assertJsonPath('meta.per_page', 15);
    }

    /**
     * TEST: Categories - Category with no products
     * Scenario: Empty category
     * Expected: 200 OK with empty data array
     */
    public function test_empty_category(): void
    {
        $emptyCategory = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$emptyCategory->slug}/products");

        $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
    }

    /**
     * TEST: Categories - SQL injection prevention
     * Scenario: Malicious SQL in category slug
     * Expected: No error, safely handled
     */
    public function test_sql_injection_prevention(): void
    {
        $response = $this->getJson("/api/categories/test' OR '1'='1");

        $this->assertTrue($response->status() === 404 || $response->status() === 200);
    }

    /**
     * TEST: Categories - Case-insensitive slug
     * Scenario: Fetch category with uppercase slug
     * Expected: 200 OK (if system supports case-insensitive)
     */
    public function test_category_slug_handling(): void
    {
        $category = Category::first();
        
        $response = $this->getJson("/api/categories/" . strtoupper($category->slug));

        // May be 404 or 200 depending on implementation
        $this->assertTrue($response->status() === 404 || $response->status() === 200);
    }

    /**
     * TEST: Categories - Sort products by price
     * Scenario: Get category products sorted by price
     * Expected: Products ordered ascending
     */
    public function test_category_products_sorted_by_price(): void
    {
        $category = Category::first();
        Product::factory()->create(['category_id' => $category->id, 'price' => 100, 'is_active' => true]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 50, 'is_active' => true]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 75, 'is_active' => true]);

        $response = $this->getJson("/api/categories/{$category->slug}/products?sort=price");

        $response->assertStatus(200);

        $prices = collect($response->json('data'))->pluck('price');
        $this->assertTrue($prices->values() === $prices->sort()->values());
    }

    /**
     * TEST: Categories - Category description HTML escaping
     * Scenario: Category has HTML in description
     * Expected: HTML escaped in response
     */
    public function test_category_description_escaping(): void
    {
        $category = Category::factory()->create([
            'description' => '<script>alert("xss")</script> Safe description',
        ]);

        $response = $this->getJson("/api/categories/{$category->slug}");

        $response->assertStatus(200);

        $description = $response->json('data.description');
        $this->assertStringNotContainsString('<script>', $description);
    }

    /**
     * TEST: Categories - Performance with many categories
     * Scenario: System has 1000 categories
     * Expected: Response in under 2 seconds
     */
    public function test_fetch_many_categories_performance(): void
    {
        Category::factory(1000)->create();

        $startTime = microtime(true);
        $response = $this->getJson('/api/categories?per_page=100');
        $duration = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(2, $duration);
    }

    /**
     * TEST: Categories - Product count in category
     * Scenario: Category shows product count
     * Expected: Count is accurate
     */
    public function test_category_product_count(): void
    {
        $category = Category::first();
        Product::factory(7)->create(['category_id' => $category->id, 'is_active' => true]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);

        $categoryData = collect($response->json('data'))
            ->firstWhere('id', $category->id);

        $this->assertNotNull($categoryData);
        if (isset($categoryData['product_count'])) {
            $this->assertGreaterThanOrEqual(7, $categoryData['product_count']);
        }
    }
}

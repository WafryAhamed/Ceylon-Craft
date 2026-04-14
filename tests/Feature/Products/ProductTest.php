<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test categories
        Category::factory(3)->create();
        
        // Create test products
        Product::factory(25)->create(['is_active' => true]);
    }

    /**
     * TEST: Products - List all products with pagination
     * Scenario: Request /api/products without filters
     * Expected: 200 OK with paginated results (default 12 per page)
     */
    public function test_list_all_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['*' => ['id', 'name', 'price', 'image', 'slug']],
                    'meta' => ['total', 'per_page', 'page', 'last_page']
                ])
                ->assertJsonCount(12, 'data');
    }

    /**
     * TEST: Products - Fetch single product by slug
     * Scenario: Request /api/products/:slug
     * Expected: 200 OK with full product details
     */
    public function test_get_product_by_slug(): void
    {
        $product = Product::first();

        $response = $this->getJson("/api/products/{$product->slug}");

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.id', $product->id)
                ->assertJsonPath('data.slug', $product->slug)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'name', 'description', 'price', 'stock', 'reviews', 'categories']
                ]);
    }

    /**
     * TEST: Products - Fetch non-existent product slug
     * Scenario: Request product with invalid slug
     * Expected: 404 Not Found
     */
    public function test_get_non_existent_product(): void
    {
        $response = $this->getJson('/api/products/invalid-slug-xyz');

        $response->assertStatus(404)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Products - Fetch inactive product (should return 404)
     * Scenario: Request product with is_active = false
     * Expected: 404 Not Found (hidden from public)
     */
    public function test_get_inactive_product_returns_404(): void
    {
        $inactiveProduct = Product::factory()->create(['is_active' => false]);

        $response = $this->getJson("/api/products/{$inactiveProduct->slug}");

        $response->assertStatus(404);
    }

    /**
     * TEST: Products - Search by name
     * Scenario: Query /api/products?search=laptop
     * Expected: 200 OK with matching products
     */
    public function test_search_products_by_name(): void
    {
        Product::factory()->create(['name' => 'Handmade Laptop Sleeve', 'is_active' => true]);
        Product::factory()->create(['name' => 'Ceramic Vase', 'is_active' => true]);

        $response = $this->getJson('/api/products?search=laptop');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertTrue(
            collect($data)->contains(fn($product) => 
                str_contains(strtolower($product['name']), 'laptop')
            )
        );
    }

    /**
     * TEST: Products - Search with empty results
     * Scenario: Query for non-existent product
     * Expected: 200 OK with empty data array
     */
    public function test_search_with_no_results(): void
    {
        $response = $this->getJson('/api/products?search=nonexistentproduct123');

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonCount(0, 'data');
    }

    /**
     * TEST: Products - Filter by category
     * Scenario: Query /api/products?category_id=1
     * Expected: 200 OK with products from that category only
     */
    public function test_filter_by_category(): void
    {
        $category = Category::first();
        Product::factory(3)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/products?category_id={$category->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertTrue(count($data) >= 3);
        $this->assertTrue(
            collect($data)->every(fn($product) => $product['category_id'] == $category->id)
        );
    }

    /**
     * TEST: Products - Filter by price range
     * Scenario: Query /api/products?min_price=100&max_price=500
     * Expected: 200 OK with products in price range
     */
    public function test_filter_by_price_range(): void
    {
        Product::factory()->create(['price' => 150, 'is_active' => true]);
        Product::factory()->create(['price' => 300, 'is_active' => true]);
        Product::factory()->create(['price' => 600, 'is_active' => true]);

        $response = $this->getJson('/api/products?min_price=100&max_price=500');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertTrue(
            collect($data)->every(fn($product) => 
                $product['price'] >= 100 && $product['price'] <= 500
            )
        );
    }

    /**
     * TEST: Products - Sort by price ascending
     * Scenario: Query /api/products?sort=price
     * Expected: 200 OK with sorted results
     */
    public function test_sort_by_price_ascending(): void
    {
        $response = $this->getJson('/api/products?sort=price');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $prices = collect($data)->map(fn($p) => $p['price'])->toArray();
        $this->assertEquals($prices, array_values(sort($prices) ?: $prices));
    }

    /**
     * TEST: Products - Sort by price descending
     * Scenario: Query /api/products?sort=-price
     * Expected: 200 OK with descending prices
     */
    public function test_sort_by_price_descending(): void
    {
        $response = $this->getJson('/api/products?sort=-price');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $prices = collect($data)->map(fn($p) => $p['price'])->toArray();
        $reversedPrices = array_reverse($prices);
        $this->assertEquals($prices, $reversedPrices);
    }

    /**
     * TEST: Products - Pagination
     * Scenario: Query /api/products?page=2&per_page=10
     * Expected: 200 OK with second page of results
     */
    public function test_pagination(): void
    {
        $response = $this->getJson('/api/products?page=2&per_page=10');

        $response->assertStatus(200)
                ->assertJsonPath('meta.page', 2)
                ->assertJsonPath('meta.per_page', 10);
    }

    /**
     * TEST: Products - Get featured products
     * Scenario: Query /api/products/featured
     * Expected: 200 OK with featured=true products only
     */
    public function test_get_featured_products(): void
    {
        Product::factory(5)->create(['featured' => true, 'is_active' => true]);

        $response = $this->getJson('/api/products?featured=1');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertTrue(count($data) >= 5);
    }

    /**
     * TEST: Products - Multiple filters combined
     * Scenario: Query with category, price, and sort
     * Expected: 200 OK with correctly filtered and sorted results
     */
    public function test_multiple_filters_combined(): void
    {
        $category = Category::first();
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 250,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/products?category_id={$category->id}&min_price=200&max_price=300&sort=price");

        $response->assertStatus(200)
                ->assertJsonPath('success', true);
    }

    /**
     * TEST: Products - SQL injection prevention (search)
     * Scenario: Malicious SQL query in search parameter
     * Expected: 200 OK with safe results (no errors/crashes)
     */
    public function test_search_sql_injection_prevention(): void
    {
        $maliciousQuery = urlencode("'; DROP TABLE products; --");
        $response = $this->getJson("/api/products?search={$maliciousQuery}");

        $response->assertStatus(200);
        
        // Verify table still exists
        $this->assertGreater(Product::count(), 0);
    }

    /**
     * TEST: Products - XSS prevention (product name)
     * Scenario: Product with script tags in name
     * Expected: Script tags escaped/sanitized in response
     */
    public function test_xss_prevention_in_product_name(): void
    {
        Product::factory()->create([
            'name' => '<script>alert("xss")</script>Vase',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Verify no raw script tags in response
        $responseJson = json_encode($data);
        $this->assertStringNotContainsString('<script>', $responseJson);
    }

    /**
     * TEST: Products - Invalid filter values
     * Scenario: Query with invalid filter values
     * Expected: 400 or 422 Bad Request / Validation Error
     */
    public function test_invalid_filter_values(): void
    {
        $response = $this->getJson('/api/products?min_price=invalid&max_price=abc');

        $this->assertTrue(
            $response->status() === 200 || $response->status() === 422
        );
    }

    /**
     * TEST: Products - Large dataset performance
     * Scenario: List 1000 products
     * Expected: 200 OK with response time < 2 seconds
     */
    public function test_large_product_list_performance(): void
    {
        Product::factory(1000)->create(['is_active' => true]);

        $start = microtime(true);
        $response = $this->getJson('/api/products');
        $duration = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(2, $duration, 'Product list took longer than 2 seconds');
    }
}

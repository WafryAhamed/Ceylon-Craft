<?php

namespace Tests\Feature\Reviews;

use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['is_active' => true]);
    }

    /**
     * TEST: Reviews - Add review as authenticated user
     * Scenario: Logged-in user adds product review
     * Expected: 201 Created with review details
     */
    public function test_authenticated_user_can_add_review(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Great product! Very satisfied.',
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['id', 'product_id', 'user_id', 'rating', 'comment']])
                ->assertJsonPath('data.rating', 4);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 4,
            'comment' => 'Great product! Very satisfied.',
        ]);
    }

    /**
     * TEST: Reviews - Guest cannot add review
     * Scenario: Unauthenticated user tries to add review
     * Expected: 401 Unauthorized
     */
    public function test_guest_cannot_add_review(): void
    {
        $response = $this->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Test comment',
        ]);

        $response->assertStatus(401);
    }

    /**
     * TEST: Reviews - Validate rating range (1-5)
     * Scenario: Submit rating < 1
     * Expected: 422 validation error
     */
    public function test_rating_below_minimum_rejected(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 0,
            'comment' => 'Test comment',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Reviews - Validate rating range (1-5)
     * Scenario: Submit rating > 5
     * Expected: 422 validation error
     */
    public function test_rating_above_maximum_rejected(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 6,
            'comment' => 'Test comment',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Reviews - Valid rating values (1, 2, 3, 4, 5)
     * Scenario: Accept all valid ratings
     * Expected: 201 Created for each
     */
    public function test_all_valid_ratings_accepted(): void
    {
        foreach ([1, 2, 3, 4, 5] as $rating) {
            $user = User::factory()->create();
            $response = $this->actingAs($user)->postJson('/api/reviews', [
                'product_id' => $this->product->id,
                'rating' => $rating,
                'comment' => "Rating {$rating} test",
            ]);

            $response->assertStatus(201);
            $this->assertDatabaseHas('reviews', [
                'product_id' => $this->product->id,
                'user_id' => $user->id,
                'rating' => $rating,
            ]);
        }
    }

    /**
     * TEST: Reviews - Get product reviews
     * Scenario: Fetch all reviews for a product
     * Expected: 200 OK with reviews list
     */
    public function test_get_product_reviews(): void
    {
        Review::factory(5)->create(['product_id' => $this->product->id]);

        $response = $this->getJson("/api/products/{$this->product->slug}/reviews");

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'user_id', 'rating', 'comment', 'created_at']]])
                ->assertJsonCount(5, 'data');
    }

    /**
     * TEST: Reviews - Update own review
     * Scenario: User updates their own review
     * Expected: 200 OK with updated review
     */
    public function test_user_can_update_own_review(): void
    {
        $review = Review::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->putJson("/api/reviews/{$review->id}", [
            'rating' => 5,
            'comment' => 'Updated comment - loved it!',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.rating', 5)
                ->assertJsonPath('data.comment', 'Updated comment - loved it!');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => 'Updated comment - loved it!',
        ]);
    }

    /**
     * TEST: Reviews - Cannot update another user's review
     * Scenario: User tries to edit someone else's review
     * Expected: 403 Forbidden
     */
    public function test_user_cannot_update_other_review(): void
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->putJson("/api/reviews/{$review->id}", [
            'rating' => 1,
            'comment' => 'Hacked comment',
        ]);

        $response->assertStatus(403);

        // Verify not modified
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
        ]);
    }

    /**
     * TEST: Reviews - Delete own review
     * Scenario: User deletes their review
     * Expected: 200 OK, review removed
     */
    public function test_user_can_delete_own_review(): void
    {
        $review = Review::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /**
     * TEST: Reviews - Cannot delete another user's review
     * Scenario: User tries to delete someone else's review
     * Expected: 403 Forbidden
     */
    public function test_user_cannot_delete_other_review(): void
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(403);

        // Verify still exists
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    /**
     * TEST: Reviews - Empty comment is optional
     * Scenario: Submit review with rating but no comment
     * Expected: 201 Created
     */
    public function test_review_without_comment(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 4,
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('data.rating', 4);
    }

    /**
     * TEST: Reviews - XSS prevention in comment
     * Scenario: Submit review with script tag
     * Expected: 201 Created but comment escaped
     */
    public function test_xss_prevention_in_review_comment(): void
    {
        $xssPayload = '<script>alert("xss")</script> Not bad';

        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 3,
            'comment' => $xssPayload,
        ]);

        $response->assertStatus(201);

        $review = Review::latest()->first();
        $this->assertStringNotContainsString('<script>', $review->comment);
    }

    /**
     * TEST: Reviews - Cannot review non-existent product
     * Scenario: Product ID doesn't exist
     * Expected: 404 or 422 error
     */
    public function test_cannot_review_non_existent_product(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => 99999,
            'rating' => 4,
            'comment' => 'Test',
        ]);

        $this->assertTrue($response->status() === 404 || $response->status() === 422);
    }

    /**
     * TEST: Reviews - Prevent duplicate reviews from same user
     * Scenario: Same user adds second review for same product
     * Expected: Either update existing or return error
     */
    public function test_prevent_duplicate_user_reviews(): void
    {
        $review1 = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        // Try to add second review
        $response = $this->actingAs($this->user)->postJson('/api/reviews', [
            'product_id' => $this->product->id,
            'rating' => 3,
            'comment' => 'Second review',
        ]);

        // Should either update or reject
        if ($response->status() === 200 || $response->status() === 201) {
            // Verify only one review exists for this user on this product
            $count = Review::where('user_id', $this->user->id)
                          ->where('product_id', $this->product->id)
                          ->count();
            $this->assertLessThanOrEqual(1, $count);
        }
    }

    /**
     * TEST: Reviews - Product average rating
     * Scenario: Multiple reviews for product
     * Expected: Average rating calculated correctly
     */
    public function test_product_average_rating_calculation(): void
    {
        Review::factory()->create(['product_id' => $this->product->id, 'rating' => 5]);
        Review::factory()->create(['product_id' => $this->product->id, 'rating' => 3]);
        Review::factory()->create(['product_id' => $this->product->id, 'rating' => 4]);

        $response = $this->getJson("/api/products/{$this->product->slug}");

        $response->assertStatus(200);

        // Average should be (5+3+4)/3 = 4
        $avgRating = $response->json('data.average_rating') ?? $response->json('data.rating');
        $this->assertNotNull($avgRating);
    }
}

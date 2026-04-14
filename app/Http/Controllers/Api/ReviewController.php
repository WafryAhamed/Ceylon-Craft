<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product.
     */
    public function index(Request $request, $slug = null): JsonResponse
    {
        // Get product_id from route parameter (slug) or query parameter
        $productId = null;
        
        if ($slug) {
            // Look up product by slug
            $product = \App\Models\Product::where('slug', $slug)->firstOrFail();
            $productId = $product->id;
        } else {
            $productId = $request->input('product_id');
        }

        if (!$productId) {
            return response()->json([
                'success' => false,
                'message' => 'Product ID is required',
            ], 400);
        }

        $reviews = Review::forProduct($productId)
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ],
        ], 200);
    }

    /**
     * Store a new review.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $request->input('product_id'))
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product',
            ], 422);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->input('product_id'),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review,
        ], 201);
    }

    /**
     * Update a review.
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        // Authorize user
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review,
        ], 200);
    }

    /**
     * Delete a review.
     */
    public function destroy(Request $request, Review $review): JsonResponse
    {
        // Authorize user
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }

    /**
     * Get review statistics for a product.
     */
    public function stats(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');

        if (!$productId) {
            return response()->json([
                'success' => false,
                'message' => 'Product ID is required',
            ], 400);
        }

        $stats = [
            'average_rating' => Review::forProduct($productId)->avg('rating') ?? 0,
            'total_reviews' => Review::forProduct($productId)->count(),
            'rating_distribution' => [],
        ];

        // Get distribution of ratings
        for ($i = 5; $i >= 1; $i--) {
            $stats['rating_distribution'][$i] = Review::forProduct($productId)
                ->byRating($i)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ], 200);
    }
}

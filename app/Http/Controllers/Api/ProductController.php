<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->active();

        // Search by name or description
        if ($request->has('search')) {
            $query->search($request->input('search'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->byCategory($request->input('category_id'));
        }

        // Filter by category slug
        if ($request->has('category')) {
            $category = \App\Models\Category::where('slug', $request->input('category'))->first();
            if ($category) {
                $query->byCategory($category->id);
            }
        }

        // Price range filter
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->priceRange(
                $request->input('min_price'),
                $request->input('max_price')
            );
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $per_page = $request->input('per_page', 12);
        $products = $query->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Display a single product by slug.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['categories', 'reviews.user'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->image_url,
                'is_active' => $product->is_active,
                'average_rating' => $product->averageRating(),
                'review_count' => $product->reviews()->count(),
                'categories' => $product->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]),
                'reviews' => $product->reviews()
                    ->with('user')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn ($review) => [
                        'id' => $review->id,
                        'user' => $review->user->name,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->diffForHumans(),
                    ]),
            ],
        ]);
    }

    /**
     * Store a newly created product (admin only).
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug from name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $product = Product::create($validated);

        // Attach categories if provided
        if (isset($validated['categories'])) {
            $product->categories()->attach($validated['categories']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Update the specified product (admin only).
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug from name if changed
        if (isset($validated['name']) && $validated['name'] !== $product->name) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $product->update($validated);

        // Sync categories if provided
        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * Delete the specified product (admin only).
     */
    public function destroy(Product $product): JsonResponse
    {
        // Delete image
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }

    /**
     * Toggle product active status (admin only).
     */
    public function toggleActive(Product $product): JsonResponse
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'data' => ['is_active' => $product->is_active],
        ], 200);
    }

    /**
     * Toggle product featured status (admin only).
     */
    public function toggleFeatured(Product $product): JsonResponse
    {
        $product->featured = !$product->featured;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product featured status updated successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * Get featured products.
     */
    public function featured(): JsonResponse
    {
        $products = Product::active()
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q');

        if (!$term || strlen($term) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search term must be at least 2 characters',
            ], 400);
        }

        $products = Product::active()
            ->search($term)
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}

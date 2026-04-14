<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->with(['products' => function ($q) {
                $q->where('is_active', true)->limit(5);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'description' => $cat->description,
                'image' => $cat->image ? asset('storage/' . $cat->image) : null,
                'product_count' => $cat->products()->where('is_active', true)->count(),
            ]),
        ]);
    }

    /**
     * Get a single category with products.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                  ->paginate(12);
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'products' => $category->products()->where('is_active', true)->paginate(12),
            ],
        ]);
    }
}

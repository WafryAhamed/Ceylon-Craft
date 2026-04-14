<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get all users (paginated).
     */
    public function listUsers(Request $request): JsonResponse
    {
        $per_page = $request->input('per_page', 15);
        $users = User::paginate($per_page);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Get admin dashboard statistics.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');

        $recentOrders = Order::latest()->limit(5)->get();
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'statistics' => [
                    'total_users' => $totalUsers,
                    'total_orders' => $totalOrders,
                    'total_products' => $totalProducts,
                    'total_revenue' => $totalRevenue,
                ],
                'recent_orders' => $recentOrders->map(fn ($order) => [
                    'id' => $order->id,
                    'user' => $order->user->name,
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toDateTimeString(),
                ]),
                'top_products' => $topProducts->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'sales' => $product->order_items_count ?? 0,
                ]),
            ],
        ]);
    }
}

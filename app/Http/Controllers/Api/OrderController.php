<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders->items(),
            'meta' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ], 200);
    }

    /**
     * Get a specific order.
     */
    public function show(Order $order, Request $request): JsonResponse
    {
        // Authorize user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city,
                'shipping_postal_code' => $order->shipping_postal_code,
                'notes' => $order->notes,
                'items' => $order->items()->with('product')->get()->map(fn ($item) => [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image_url,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->getTotalPrice(),
                ]),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
        ]);
    }

    /**
     * Create order from cart (checkout).
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $cart = $user->cart;

        if (!$cart || $cart->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        return DB::transaction(function () use ($user, $cart, $request) {
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $cart->getTotalPrice(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->input('payment_method', 'cod'),
                'shipping_address' => $request->input('address') ?? $request->input('shipping_address'),
                'shipping_city' => $request->input('shipping_city'),
                'shipping_postal_code' => $request->input('postal_code') ?? $request->input('shipping_postal_code'),
                'notes' => $request->input('notes'),
            ]);

            // Add order items and reduce stock
            foreach ($cart->items()->with('product')->get() as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Reduce product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            $cart->clear();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'total' => $order->total,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ],
            ], 201);
        });
    }

    /**
     * Update order status (admin only).
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,paid,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $order->update($request->only(['status', 'payment_status']));

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order,
        ], 200);
    }

    /**
     * Toggle order status (admin only).
     */
    public function toggleStatus(Request $request, Order $order): JsonResponse
    {
        if (!$request->user()?->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $statusMap = [
            'pending' => 'paid',
            'paid' => 'shipped',
            'shipped' => 'delivered',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
        ];

        $newStatus = $statusMap[$order->status] ?? 'pending';
        $order->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Order status toggled successfully',
            'data' => ['status' => $newStatus],
        ], 200);
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        // Authorize user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'paid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order with status: ' . $order->status,
            ], 400);
        }

        return DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->markAsCancelled();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
            ], 200);
        });
    }

    /**
     * Get all orders (admin only).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Order::query();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment status if provided
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Search by customer email or name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }

        $orders = $query
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders->items(),
            'meta' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ], 200);
    }

    /**
     * Get order statistics (admin only).
     */
    public function stats(Request $request): JsonResponse
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total');
        $totalProducts = Order::query()
            ->selectRaw('COUNT(DISTINCT product_id) as total')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->value('total') ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'Order statistics retrieved successfully',
            'data' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'total_revenue' => number_format((float)$totalRevenue, 2),
                'total_products' => Product::count(),
                'recent_orders' => Order::with(['user', 'items.product'])
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn ($order) => [
                        'id' => $order->id,
                        'customer' => $order->user->name,
                        'total' => $order->total,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    ]),
            ],
        ]);
    }
}

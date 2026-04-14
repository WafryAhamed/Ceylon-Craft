<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get the user's cart.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $request->user()->cart()->with(['items.product'])->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $request->user()->id]);
        }

        $items = $cart->items()->with('product')->get()->map(fn ($item) => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => $item->product->price,
                'image' => $item->product->image_url,
            ],
            'quantity' => $item->quantity,
            'total' => $item->getTotalPrice(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ],
        ], 200);
    }

    /**
     * Add item to cart.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $user = $request->user();
        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);

        $product = Product::findOrFail($request->input('product_id'));
        $quantity = $request->input('quantity', 1);

        // Validate quantity
        if ($quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Quantity must be greater than 0',
            ], 422);
        }

        // Check stock
        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
            ], 409);
        }

        // Add or update cart item
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available',
                ], 409);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'data' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ],
        ], 200);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $quantity = $request->input('quantity');

        // Check stock
        if ($cartItem->product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
            ], 409);
        }

        $cartItem->update(['quantity' => $quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated',
            'data' => [
                'total_price' => $cartItem->getTotalPrice(),
            ],
        ], 200);
    }

    /**
     * Update cart item by cart_item_id in request body (alternative endpoint).
     */
    public function updateByBody(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        $cartItem = CartItem::findOrFail($request->input('cart_item_id'));
        $quantity = $request->input('quantity');

        // If quantity is 0, delete the item
        if ($quantity === 0) {
            $cart = $cartItem->cart;
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart item removed',
                'data' => [
                    'total_items' => $cart->getTotalItems(),
                    'total_price' => $cart->getTotalPrice(),
                ],
            ], 200);
        }

        // Check stock
        if ($cartItem->product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
            ], 409);
        }

        $cartItem->update(['quantity' => $quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated',
            'data' => [
                'total_price' => $cartItem->getTotalPrice(),
            ],
        ], 200);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(CartItem $cartItem): JsonResponse
    {
        $cart = $cartItem->cart;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ],
        ], 200);
    }

    /**
     * Remove item from cart by cart_item_id in request body (alternative endpoint).
     */
    public function deleteByBody(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
        ]);

        $cartItem = CartItem::findOrFail($request->input('cart_item_id'));
        $cart = $cartItem->cart;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ],
        ], 200);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $request->user()->cart;
        if ($cart) {
            $cart->clear();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ], 200);
    }
}

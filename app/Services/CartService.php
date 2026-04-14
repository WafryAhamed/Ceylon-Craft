<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    /**
     * Add item to cart.
     */
    public static function addToCart(User $user, int $productId, int $quantity): CartItem
    {
        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);
        $product = Product::findOrFail($productId);

        // Check stock
        if ($product->stock < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        // Add or update cart item
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock < $newQuantity) {
                throw new \Exception('Insufficient stock available');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update cart item quantity.
     */
    public static function updateCartItem(CartItem $cartItem, int $quantity): CartItem
    {
        // Check stock
        if ($cartItem->product->stock < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    /**
     * Remove cart item.
     */
    public static function removeCartItem(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    /**
     * Get cart total.
     */
    public static function getCartTotal(User $user): float
    {
        $cart = $user->cart;
        return $cart ? $cart->getTotalPrice() : 0;
    }

    /**
     * Get cart item count.
     */
    public static function getCartItemCount(User $user): int
    {
        $cart = $user->cart;
        return $cart ? $cart->getTotalItems() : 0;
    }
}

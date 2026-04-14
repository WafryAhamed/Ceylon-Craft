<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create order from cart.
     */
    public static function createOrderFromCart(
        User $user,
        array $shippingData,
        string $paymentMethod,
        ?string $notes = null
    ): Order {
        return DB::transaction(function () use ($user, $shippingData, $paymentMethod, $notes) {
            $cart = $user->cart;

            if (!$cart || $cart->items()->count() === 0) {
                throw new \Exception('Cart is empty');
            }

            // Calculate total
            $total = $cart->getTotalPrice();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_address' => $shippingData['address'],
                'shipping_city' => $shippingData['city'],
                'shipping_postal_code' => $shippingData['postal_code'],
                'notes' => $notes,
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

            return $order;
        });
    }

    /**
     * Cancel order and restore stock.
     */
    public static function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if (!in_array($order->status, ['pending', 'paid'])) {
                throw new \Exception('Cannot cancel order with status: ' . $order->status);
            }

            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->markAsCancelled();
        });
    }
}

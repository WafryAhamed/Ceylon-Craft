<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Order Notification Service
 * 
 * Handles sending order-related emails:
 * - Order confirmation
 * - Payment success
 * - Shipment notification
 * - Delivery notification
 * - Order cancellation
 */
class OrderNotificationService
{
    /**
     * Send order confirmation email.
     */
    public static function sendOrderConfirmation(Order $order): void
    {
        $user = $order->user;

        // Queue email for async sending (if Laravel queues are configured)
        \Mail::to($user->email)
            ->send(new \App\Mail\OrderConfirmationMail($order));

        \Log::info('Order confirmation email sent', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Send payment success email.
     */
    public static function sendPaymentSuccess(Order $order): void
    {
        $user = $order->user;

        \Mail::to($user->email)
            ->send(new \App\Mail\PaymentSuccessMail($order));

        \Log::info('Payment success email sent', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Send order shipped notification.
     */
    public static function sendOrderShipped(Order $order, ?string $trackingNumber = null): void
    {
        $user = $order->user;

        \Mail::to($user->email)
            ->send(new \App\Mail\OrderShippedMail($order, $trackingNumber));

        \Log::info('Order shipped email sent', [
            'order_id' => $order->id,
            'tracking' => $trackingNumber,
        ]);
    }

    /**
     * Send order delivered notification.
     */
    public static function sendOrderDelivered(Order $order): void
    {
        $user = $order->user;

        \Mail::to($user->email)
            ->send(new \App\Mail\OrderDeliveredMail($order));

        \Log::info('Order delivered email sent', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order cancellation notification.
     */
    public static function sendOrderCancelled(Order $order, ?string $reason = null): void
    {
        $user = $order->user;

        \Mail::to($user->email)
            ->send(new \App\Mail\OrderCancelledMail($order, $reason));

        \Log::info('Order cancelled email sent', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send admin notification for new order.
     */
    public static function notifyAdminNewOrder(Order $order): void
    {
        $adminEmail = config('mail.admin_email', config('mail.from.address'));

        \Mail::to($adminEmail)
            ->send(new \App\Mail\AdminNewOrderMail($order));

        \Log::info('Admin notification sent for new order', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send contact form acknowledgment.
     */
    public static function sendContactFormAcknowledgement(string $email, string $name, string $message): void
    {
        \Mail::to($email)
            ->send(new \App\Mail\ContactFormAcknowledgementMail($name, $message));

        // Also send to admin
        $adminEmail = config('mail.admin_email', config('mail.from.address'));
        \Mail::to($adminEmail)
            ->send(new \App\Mail\ContactFormNotificationMail($name, $email, $message));

        \Log::info('Contact form acknowledgement sent', [
            'email' => $email,
            'name' => $name,
        ]);
    }
}

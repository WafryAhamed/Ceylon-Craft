<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\ShipmentNotificationMail;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation(Order $order): void
    {
        try {
            Mail::to($order->user->email)
                ->send(new OrderConfirmationMail($order));
            
            Log::info('Order confirmation email sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            // Don't throw - email failures shouldn't block order creation
        }
    }

    /**
     * Send payment confirmed email
     */
    public function sendPaymentConfirmation(Payment $payment): void
    {
        try {
            Mail::to($payment->user->email)
                ->send(new PaymentConfirmedMail($payment));
            
            Log::info('Payment confirmation email sent', [
                'payment_id' => $payment->id,
                'user_email' => $payment->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send shipment notification email
     */
    public function sendShipmentNotification(
        Order $order,
        string $trackingNumber = '',
        string $carrier = 'Standard Shipping'
    ): void {
        try {
            Mail::to($order->user->email)
                ->send(new ShipmentNotificationMail($order, $trackingNumber, $carrier));
            
            Log::info('Shipment notification email sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email,
                'tracking_number' => $trackingNumber,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send shipment notification email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send delivery confirmation email
     */
    public function sendDeliveryConfirmation(Order $order): void
    {
        try {
            $emailContent = sprintf(
                "Your Ceylon Craft order #%s has been delivered to:\n\n%s\n%s %s\n\nThank you for your purchase!",
                str_pad($order->id, 6, '0', STR_PAD_LEFT),
                $order->address,
                $order->postal_code,
                $order->country
            );

            Mail::to($order->user->email)->send(
                (new \Illuminate\Mail\Message())
                    ->subject('Your Order Has Been Delivered - Ceylon Craft')
                    ->line($emailContent)
            );
            
            Log::info('Delivery confirmation email sent', [
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send refund notification email
     */
    public function sendRefundNotification(Payment $payment, float $refundAmount): void
    {
        try {
            $currencySymbol = $payment->currency === 'usd' ? '$' : '€';
            
            Mail::to($payment->user->email)
                ->send(
                    new \Illuminate\Mail\Mailable(
                        subject: 'Refund Processed - Ceylon Craft Order #' . $payment->order_id,
                        view: 'emails.payments.refund',
                        data: [
                            'order_number' => str_pad($payment->order_id, 6, '0', STR_PAD_LEFT),
                            'refund_amount' => $currencySymbol . number_format($refundAmount / 100, 2),
                            'customer_name' => $payment->user->name,
                            'refund_date' => now()->format('M d, Y'),
                            'transaction_id' => $payment->stripe_charge_id,
                        ]
                    )
                );
            
            Log::info('Refund notification email sent', [
                'payment_id' => $payment->id,
                'refund_amount' => $refundAmount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send refund notification email', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

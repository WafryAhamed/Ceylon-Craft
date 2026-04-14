<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Order $order,
        protected string $trackingNumber = '',
        protected string $carrier = 'Standard Shipping'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order Has Shipped! - Ceylon Craft #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.shipment',
            with: [
                'order' => $this->order,
                'customer_name' => $this->order->user->name,
                'order_number' => str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
                'tracking_number' => $this->trackingNumber,
                'carrier' => $this->carrier,
                'shipping_address' => [
                    'address' => $this->order->address,
                    'postal_code' => $this->order->postal_code,
                    'city' => 'Colombo', // Should be from order if available
                    'country' => 'Sri Lanka',
                ],
                'estimated_delivery' => now()->addDays(3)->format('M d, Y'),
                'tracking_url' => route('orders.track', $this->order->id),
            ],
        );
    }
}

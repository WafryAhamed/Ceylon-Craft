<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - Ceylon Craft #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.confirmation',
            with: [
                'order' => $this->order,
                'customer_name' => $this->order->user->name,
                'order_number' => str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
                'order_total' => $this->order->total,
                'order_items' => $this->order->items,
                'tracking_url' => route('orders.track', $this->order->id),
            ],
        );
    }
}

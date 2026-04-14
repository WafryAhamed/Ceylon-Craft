<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmed - Ceylon Craft Order #' . $this->payment->order_id,
        );
    }

    public function content(): Content
    {
        $order = $this->payment->order;
        
        return new Content(
            view: 'emails.payments.confirmed',
            with: [
                'order' => $order,
                'payment' => $this->payment,
                'customer_name' => $order->user->name,
                'order_number' => str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'amount' => $this->payment->amount,
                'currency' => strtoupper($this->payment->currency),
                'payment_date' => $this->payment->created_at->format('M d, Y H:i A'),
                'transaction_id' => $this->payment->stripe_charge_id ?? $this->payment->id,
            ],
        );
    }
}

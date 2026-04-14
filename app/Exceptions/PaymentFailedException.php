<?php

namespace App\Exceptions;

class PaymentFailedException extends ApiException
{
    public function __construct(string $message = 'Payment processing failed', string $paymentError = null)
    {
        $data = ['payment_error' => $paymentError];
        parent::__construct($message, 402, $data);
    }
}

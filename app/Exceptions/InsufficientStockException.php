<?php

namespace App\Exceptions;

class InsufficientStockException extends ApiException
{
    public function __construct(int $available, int $requested)
    {
        $message = "Insufficient stock. Available: {$available}, Requested: {$requested}";
        parent::__construct($message, 409, [
            'available' => $available,
            'requested' => $requested,
        ]);
    }
}

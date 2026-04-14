<?php

namespace App\Exceptions;

/**
 * Thrown when authentication fails or user lacks permission
 */
class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Unauthorized')
    {
        parent::__construct($message, 401);
    }
}

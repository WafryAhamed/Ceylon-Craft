<?php

namespace App\Exceptions;

class RateLimitedException extends ApiException
{
    public function __construct(string $message = 'Too many requests. Please try again later.')
    {
        parent::__construct($message, 429);
    }
}

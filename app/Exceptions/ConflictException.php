<?php

namespace App\Exceptions;

class ConflictException extends ApiException
{
    public function __construct(string $message = 'Conflict - resource already exists or has been modified')
    {
        parent::__construct($message, 409);
    }
}

<?php

namespace App\Exceptions;

/**
 * Thrown when user lacks required permission/role
 */
class ForbiddenException extends ApiException
{
    public function __construct(string $message = 'Forbidden - insufficient permissions')
    {
        parent::__construct($message, 403);
    }
}

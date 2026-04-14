<?php

namespace App\Exceptions;

/**
 * Thrown when a resource is not found
 */
class ResourceNotFoundException extends ApiException
{
    public function __construct(string $resourceType = 'Resource', string $identifier = '')
    {
        $message = "{$resourceType} not found";
        if ($identifier) {
            $message .= " ({$identifier})";
        }
        parent::__construct($message, 404);
    }
}

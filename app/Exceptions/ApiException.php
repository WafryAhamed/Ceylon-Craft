<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Base API Exception for standardized error responses
 */
class ApiException extends Exception
{
    protected int $statusCode = 400;
    protected mixed $errorData = null;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 400,
        mixed $errorData = null,
        Exception $previous = null
    ) {
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->errorData = $errorData;
        
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorData(): mixed
    {
        return $this->errorData;
    }

    public function toResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'data' => $this->errorData,
            'timestamp' => now()->toIso8601String(),
        ], $this->statusCode);
    }
}

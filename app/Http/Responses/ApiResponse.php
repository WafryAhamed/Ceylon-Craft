<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Standard API Response Helper
 * 
 * All API responses follow this format:
 * {
 *   "success": bool,
 *   "message": string,
 *   "data": any,
 *   "timestamp": ISO8601
 * }
 */
class ApiResponse
{
    /**
     * Send success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = 'Request successful',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Send error response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(
        string $message = 'An error occurred',
        mixed $data = null,
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Send paginated response.
     *
     * @param mixed $items
     * @param string $message
     * @return JsonResponse
     */
    public static function paginated($items, string $message = 'Request successful'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $items->items(),
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Send created response (201).
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Send no content response (204).
     *
     * @return JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Send unauthorized response (401).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Send forbidden response (403).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Send not found response (404).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Send validation error response (422).
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function unprocessable(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    /**
     * Send conflict response (409).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function conflict(string $message = 'Conflict'): JsonResponse
    {
        return self::error($message, null, 409);
    }

    /**
     * Send too many requests response (429).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function tooManyRequests(string $message = 'Too many requests'): JsonResponse
    {
        return self::error($message, null, 429);
    }

    /**
     * Send internal server error response (500).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function internalError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, null, 500);
    }
}

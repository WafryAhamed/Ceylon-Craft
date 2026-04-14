<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware aliases for easy reference
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'api-token' => \App\Http\Middleware\ApiToken::class,
        ]);

        // API-specific middleware
        $middleware->api(prepend: [
            // Rate limiting by IP for all requests
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        ])->stateful(['laravel_session']);

        // Rate limiting for sensitive endpoints
        // Applied per-route in routes/api.php
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle API exceptions
        $exceptions->render(function (Throwable $e) {
            // Handle custom API exceptions
            if ($e instanceof \App\Exceptions\ApiException) {
                return $e->toResponse();
            }

            // Handle Laravel validation exceptions
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => $e->errors(),
                    'timestamp' => now()->toIso8601String(),
                ], 422);
            }

            // Handle not found exceptions
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'timestamp' => now()->toIso8601String(),
                ], 404);
            }

            // Handle rate limiting
            if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                    'timestamp' => now()->toIso8601String(),
                ], 429);
            }

            // Handle unauthorized exceptions
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - authentication required',
                    'timestamp' => now()->toIso8601String(),
                ], 401);
            }

            // Handle authorization exceptions
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden - insufficient permissions',
                    'timestamp' => now()->toIso8601String(),
                ], 403);
            }

            // Handle HTTP exceptions
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'timestamp' => now()->toIso8601String(),
                ], $e->getStatusCode());
            }

            // Development: show full error, production: generic message
            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Internal server error',
                    'exception' => class_basename($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toIso8601String(),
                ], 500);
            }

            // Production: generic error
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'timestamp' => now()->toIso8601String(),
            ], 500);
        });
    })->create();

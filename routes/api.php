<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AdminController;

// =====================================================================
// PUBLIC ROUTES (No Authentication Required)
// =====================================================================

// Auth routes with rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Product routes (no rate limiting, high traffic expected)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/products/{slug}/reviews', [ReviewController::class, 'index']);

// Protected product creation (requires admin)
Route::middleware(['api-token', 'admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
});

// Category routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Review routes
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/stats', [ReviewController::class, 'stats']);

// =====================================================================
// PROTECTED ROUTES (Require Authentication)
// =====================================================================

Route::middleware('api-token')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'store']);
    Route::put('/cart/update', [CartController::class, 'updateByBody']);
    Route::put('/cart/items/{cartItem}', [CartController::class, 'update']);
    // Support both DELETE methods with and without body
    Route::delete('/cart/delete', [CartController::class, 'deleteByBody']);
    Route::delete('/cart/remove', [CartController::class, 'deleteByBody']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy']);
    Route::post('/cart/clear', [CartController::class, 'clear']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'checkout']);
    Route::post('/orders/checkout', [OrderController::class, 'checkout']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Review routes
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Payment routes with rate limiting (sensitive operations)
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/payments/intent', [PaymentController::class, 'createIntent']);
        Route::post('/payments/confirm', [PaymentController::class, 'confirmPayment']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    });
});

// =====================================================================
// ADMIN ROUTES (Require Authentication + Admin Role)
// =====================================================================

Route::middleware(['api-token', 'admin'])->group(function () {
    // User management
    Route::get('/admin/users', [AdminController::class, 'listUsers']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    // Product management
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::put('/admin/products/{product}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
    Route::patch('/admin/products/{product}/toggle', [ProductController::class, 'toggleActive']);
    Route::patch('/admin/products/{product}/featured', [ProductController::class, 'toggleFeatured']);

    // Order management
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::get('/admin/orders/stats', [OrderController::class, 'stats']);
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/admin/orders/{order}', [OrderController::class, 'updateStatus']);
    Route::patch('/admin/orders/{order}/toggle', [OrderController::class, 'toggleStatus']);
});



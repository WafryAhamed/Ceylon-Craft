<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/', function () {
    return view('index');
});

// API Routes
Route::get('/api/products', [ProductController::class, 'index']);

// Catch-all route for Vue Router - must be last
Route::fallback(function () {
    return view('index');
});

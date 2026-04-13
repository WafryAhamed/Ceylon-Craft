<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

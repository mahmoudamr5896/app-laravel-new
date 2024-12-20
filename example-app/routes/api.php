<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php

use App\Http\Controllers\CategoryController;

Route::apiResource('categories', CategoryController::class);
// routes/api.php

use App\Http\Controllers\ProductController;

Route::apiResource('products', ProductController::class);

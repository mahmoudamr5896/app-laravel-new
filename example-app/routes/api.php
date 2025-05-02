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

use App\Http\Controllers\UserController;

// Route::apiResource('users', UserController::class);
Route::post('/login', [UserController::class, 'findByEmailAndPassword']);

use App\Http\Controllers\SubcategoryController;
Route::apiResource('subcategories', SubcategoryController::class);

Route::get('/categories/{id}/subcategories', [CategoryController::class, 'getSubcategories']);

Route::get('/products/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);
use App\Http\Controllers\CartController;

Route::middleware([])->group(function () {

    // View cart
Route::get('/cart/{userId}', [CartController::class, 'viewCart']);
    
    // Add product to cart
    Route::post('/cart/add', [CartController::class, 'addProduct']);
    
    // Update product quantity in cart
    Route::put('/cart/update', [CartController::class, 'updateProductQuantity']);
    
    // Remove product from cart
    Route::delete('/cart/remove', [CartController::class, 'removeProduct']);
    
    // Clear the cart
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
});
Route::patch('products/{productId}/increment-likes', [ProductController::class, 'incrementLikes']);
use App\Http\Controllers\WishlistController;

// Route::middleware([])->group(function () {
//     // Route::post('/wishlist', [WishlistController::class, 'store']);
//     // Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);
//     Route::delete('/wishlist/{productId}', [WishlistController::class, 'removeFromWishlist']);
//     Route::get('/wishlist', [WishlistController::class, 'getUserWishlist']);
// });
Route::get('/wishlist/user/{userId}', [WishlistController::class, 'getWishlistByUserId']);
Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);
// Route::delete('/wishlist/{productId}', [WishlistController::class, 'removeFromWishlist']);
Route::get('/wishlist', [WishlistController::class, 'getUserWishlist']);
Route::get('/wishlist/status', [WishlistController::class, 'checkWishlistStatus']);
use App\Http\Controllers\PaymentController;

// Route::middleware(['auth'])->group(function () {
    Route::post('/payments', [PaymentController::class, 'store']); // Create a payment
    Route::get('/payments', [PaymentController::class, 'index']); // Fetch user payments
// });

Route::post('/clear-cart/{userId}', [CartController::class, 'clearCartByUserId']);
Route::post('wishlist/remove', [WishlistController::class, 'removeFromWishlist']);
Route::patch('products/{productId}/decrement-likes', [ProductController::class, 'decrementLikes']);
#
Route::post('users', [UserController::class, 'store']);
Route::get('/verify-email', [UserController::class, 'verifyEmail']);
Route::post('/login', [UserController::class, 'findByEmailAndPassword']);
Route::get('/users', [UserController::class, 'index']);

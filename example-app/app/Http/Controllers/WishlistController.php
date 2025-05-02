<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class WishlistController extends Controller
{
    // Add a product to the wishlist
    public function addToWishlist(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id', // Ensure the product exists
                'user_id' => 'required|exists:users,id', // Ensure the user exists
            ]);
    
            // Check if the product is already in the wishlist
            $existingWishlist = Wishlist::where('user_id', $validatedData['user_id'])
                ->where('product_id', $validatedData['product_id'])
                ->first();
    
            // if ($existingWishlist ) {
            //     return response()->json(['message' => 'Product already in wishlist'], 400);
            // }
    
            // Add product to wishlist
            $wishlist = Wishlist::create($validatedData);
    
            return response()->json([
                'message' => 'Product added to wishlist successfully',
                'data' => $wishlist,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding to wishlist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
    
        // Ensure the user is authenticated
        $userId = auth()->id(); // Assuming you're using authentication
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Check if the product is already in the user's wishlist
        $wishlistExists = Wishlist::where('user_id', $userId)
            ->where('product_id', $validatedData['product_id'])
            ->exists();
    
        if ($wishlistExists) {
            return response()->json(['message' => 'Product already in wishlist'], 400);
        }
    
        // Add the product to the wishlist
        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $validatedData['product_id'],
        ]);
    
        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'data' => $wishlist,
        ], 201);
    }
    
    // Remove a product from the wishlist
    // public function removeFromWishlist($productId)
    // {
    //     $user = auth()->user(); // Assuming user is authenticated

    //     $wishlist = Wishlist::where('user_id', $user->id)
    //         ->where('product_id', $productId)
    //         ->first();

    //     if (!$wishlist) {
    //         return response()->json(['message' => 'Product not found in wishlist'], 404);
    //     }

    //     $wishlist->delete();

    //     return response()->json([
    //         'message' => 'Product removed from wishlist successfully',
    //     ]);
    // }
    public function removeFromWishlist(Request $request)
{
    $userId = $request->input('user_id');  // Get user_id from the request
    $productId = $request->input('product_id');  // Get product_id from the request

    // Validate if both user_id and product_id are provided
    if (!$userId || !$productId) {
        return response()->json(['message' => 'User ID and Product ID are required'], 400);
    }

    $wishlist = Wishlist::where('user_id', $userId)
        ->where('product_id', $productId)
        ->first();

    if (!$wishlist) {
        return response()->json(['message' => 'Product not found in wishlist'], 404);
    }

    $wishlist->delete();

    return response()->json([
        'message' => 'Product removed from wishlist successfully',
    ]);
}

    public function checkWishlistStatus(Request $request)
    {
        // Extract product_id and user_id from query parameters
        $userId = $request->query('user_id');
        $productId = $request->query('product_id');

        // Check if the product exists in the user's wishlist
        $wishlistItem = Wishlist::where('user_id', $userId)
                                ->where('product_id', $productId)
                                ->first();

        if ($wishlistItem) {
            // If the item exists in the wishlist, return true
            return response()->json([
                'isInWishlist' => true
            ]);
        } else {
            // If the item is not in the wishlist, return false
            return response()->json([
                'isInWishlist' => false
            ]);
        }
    }
    public function decrementLikes($productId)
    {
        // Find the product by its ID
        $product = Product::find($productId);
    
        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Check if likes are greater than 0 before decrementing
        if ($product->likes > 0) {
            $product->likes--;  // Decrement likes
            $product->save();
            
            return response()->json([
                'message' => 'Product likes decremented successfully',
                'likes' => $product->likes  // Return the updated likes count
            ]);
        }
    
        // If likes are 0, return a message indicating no likes to decrement
        return response()->json(['message' => 'Cannot decrement likes. Likes are already 0'], 400);
    }
    
 // Get all products in the wishlist by a specific user ID
public function getWishlistByUserId($userId)
{
    // Check if the user exists
    $user = User::find($userId);

    if (!$user) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

    // Fetch wishlist items for the specified user ID
    $wishlist = Wishlist::with('product')->where('user_id', $userId)->get();

    return response()->json([
        'message' => 'Wishlist fetched successfully',
        'data' => $wishlist,
    ]);
}

}

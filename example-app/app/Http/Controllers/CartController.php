<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * View the cart for a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function viewCart(Request $request, $userId)
    {
        // Fetch the cart based on user ID
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found for the given user ID.'], 404);
        }

        // Return the cart and its products
        return response()->json(['cart' => $cart]);
    }

    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addProduct(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product' => 'required|array',
            'product.id' => 'required|exists:products,id',
            // Add other product validation rules if needed
        ]);
    
        $userId = $validated['user_id'];
        $product = $validated['product'];
    
        // Find or create the cart for the user
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['products' => json_encode([])] // Initialize products as an empty array if cart doesn't exist
        );
    
        // Ensure products is an array (in case the JSON is null or empty)
        $products = $cart->products ? json_decode($cart->products, true) : [];
    
        // Add the new product to the array
        $products[] = $product;
    
        // Update the cart's products and save
        $cart->products = json_encode($products);
        $cart->save();
    
        return response()->json(['message' => 'Product added to cart successfully', 'cart' => $cart]);
    }
    
    /**
     * Update the quantity of a product in the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProductQuantity(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::find($validated['cart_id']);
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        // Find the product in the cart
        $products = $cart->products;
        $productKey = array_search($productId, array_column($products, 'id'));

        if ($productKey !== false) {
            // Update the quantity
            $products[$productKey]['quantity'] = $quantity;

            // Save the updated products array
            $cart->products = $products;
            $cart->save();

            return response()->json(['message' => 'Product quantity updated successfully.', 'cart' => $cart]);
        } else {
            return response()->json(['message' => 'Product not found in cart.'], 404);
        }
    }

    /**
     * Remove a product from the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeProduct(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::find($validated['cart_id']);
        $productId = $validated['product_id'];

        // Find and remove the product from the cart
        $products = $cart->products;
        $productKey = array_search($productId, array_column($products, 'id'));

        if ($productKey !== false) {
            // Remove the product
            unset($products[$productKey]);

            // Re-index the array
            $products = array_values($products);

            // Save the updated cart
            $cart->products = $products;
            $cart->save();

            return response()->json(['message' => 'Product removed from cart successfully.', 'cart' => $cart]);
        } else {
            return response()->json(['message' => 'Product not found in cart.'], 404);
        }
    }

    /**
     * Clear all products from the cart.
     *
     * @param  int  $cartId
     * @return \Illuminate\Http\Response
     */
    public function clearCart($cartId)
    {
        $cart = Cart::find($cartId);

        if ($cart) {
            $cart->products = []; // Clear all products
            $cart->save();

            return response()->json(['message' => 'Cart cleared successfully.']);
        } else {
            return response()->json(['message' => 'Cart not found.'], 404);
        }
    }
    public function clearCartByUserId($userId)
{
    // Find the cart associated with the given user ID
    $cart = Cart::where('user_id', $userId)->first();

    if ($cart) {
        // Clear the products in the cart
        $cart->products = []; // or use $cart->products()->delete() if you're using a relationship
        $cart->save();

        return response()->json(['message' => 'Cart cleared successfully.']);
    } else {
        return response()->json(['message' => 'Cart not found for this user.'], 404);
    }
}

}

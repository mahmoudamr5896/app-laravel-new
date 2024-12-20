<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request; // Add this line if you're working with categoriesuse Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Fetch all products with their associated category
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id', // Ensure category_id exists in the categories table
            ]);
    
            // Create new product
            $product = Product::create($validatedData);
    
            // Return success response
            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
    
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Error creating product: ' . $e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'An error occurred while creating the product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',  // Ensure category_id exists in categories table
        ]);

        // Update the product
        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        // Delete the product
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}

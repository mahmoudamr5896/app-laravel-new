<?php


// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Fetch all categories
        $categories = Category::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);
    
            // Create a new category
            $category = Category::create($validatedData);
    
            // Return success response
            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
    
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Error creating category: ' . $e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'An error occurred while creating the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Update the category
        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        // Delete the category
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}

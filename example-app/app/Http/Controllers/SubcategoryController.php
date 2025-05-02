<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubcategoryController extends Controller
{
    public function index()
    {
        return response()->json(Subcategory::with('category')->get(), 200);
    }

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'img' => 'nullable|string|max:255', // URL or path
    //         'category_id' => 'required|exists:categories,id',
    //     ]);

    //     $subcategory = Subcategory::create($validatedData);
    //     return response()->json($subcategory, 201);
    // }
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'img' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    //         'category_id' => 'required|exists:categories,id',
    //     ]);

    //     if ($request->hasFile('img')) {
            
    //         $path = $request->file('img')->store('subcategories', 'public');
    //         $validatedData['img'] = $path;
    //     }

    //     $subcategory = Subcategory::create($validatedData);

    //     return response()->json($subcategory, 201);
    // }

public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'img' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'category_id' => 'required|exists:categories,id',
    ]);

    if ($request->hasFile('img')) {
        $path = $request->file('img')->store('subcategories', 'public');
        $validatedData['img'] = url(Storage::url($path)); // Prepend the full URL
    }

    $subcategory = Subcategory::create($validatedData);

    return response()->json($subcategory, 201);
}

    public function show($id)
    {
        $subcategory = Subcategory::with('category')->find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        return response()->json($subcategory, 200);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'img' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $subcategory->update($validatedData);
        return response()->json($subcategory, 200);
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        $subcategory->delete();
        return response()->json(['message' => 'Subcategory deleted'], 200);
    }
}

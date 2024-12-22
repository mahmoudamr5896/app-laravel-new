<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Fetch all products with their associated category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::with('category')->get();
        
        return response()->json([
            'message' => 'Products fetched successfully',
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'img' => 'required|array',
    //         'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image files
    //     ]);

    //     // Store images in the public/products folder and get the paths
    //     $imagePaths = [];
    //     foreach ($validated['img'] as $image) {
    //         $imagePath = $image->store('products', 'public');
    //         $imagePaths[] = $imagePath; // Store the image path
    //     }

    //     // Convert the image paths to JSON format for storage
    //     $product = Product::create([
    //         'name' => $validated['name'],
    //         'price' => $validated['price'],
    //         'category_id' => $validated['category_id'],
    //         'img' => json_encode($imagePaths),  // Store as JSON string
    //     ]);

    //     return response()->json([
    //         'message' => 'Product created successfully',
    //         'data' => $product,
    //     ], 201);
    // // }
    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'img' => 'required|array',
    //         'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image files
    //     ]);
    
    //     // Store images in the public/products folder and get the paths
    //     // $imagePaths = [];
    //     // foreach ($validated['img'] as $image) {
    //     //     // Store the image and get its path
    //     //     $imagePath = $image->store('products', '${url}/public/storage');
    //     //     $imagePaths[] = $imagePath; // Store the image path
    //     // }
    //     $imagePaths = [];
    //     foreach ($validated['img'] as $image) {
    //         // Generate a unique name for the image
    //         $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();  // Random string + original file extension
    //     // Assuming $image is the uploaded image instance
    //          // $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
    //         // $imagePath = $image->storeAs('products', $imageName, 'public');
    //         // Store the image with the custom name
    //         $imagePath = $image->storeAs('products', $imageName, 'public');
            
    //         // Add the image path to the array
    //         $imagePaths[] = $imagePath;
    //     }
        
    //     // Convert the image paths to JSON format for storage
    //     $product = Product::create([
    //         'name' => $validated['name'],
    //         'price' => $validated['price'],
    //         'category_id' => $validated['category_id'],
    //         'img' => json_encode($imagePaths),  // Store as JSON string
    //     ]);
    
    //     return response()->json([
    //         'message' => 'Product created successfully',
    //         'data' => $product,
    //     ], 201);
    // }
//     public function store(Request $request)
// {
//     // Validate the request
//     $validatedData = $request->validate([
//         'name' => 'required|string|max:255',
//         'img' => 'required|array',
//         'img.*' => 'required|file|image|max:2048', // Ensure each file is a valid image
//         'price' => 'required|numeric|min:0',
//         'category_id' => 'required|exists:categories,id',
//     ]);

//     // Save images to storage and generate URLs
//     $imageUrls = [];
//     foreach ($validatedData['img'] as $image) {
//         $imagePath = $image->store('products', 'public'); // Save to 'storage/app/public/products'
//         $imageUrls[] = asset('storage/' . $imagePath); // Generate the full URL
//     }

//     // Update the img field with URLs
//     $validatedData['img'] = json_encode($imageUrls);

//     // Create the product
//     $product = Product::create($validatedData);

//     return response()->json([
//         'message' => 'Product created successfully',
//         'data' => $product,
//     ], 201);
// }
public function store(Request $request)
{
    // Validate the request
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'img' => 'required|array', // Accept multiple images as an array
        'img.*' => 'required|file|image|max:2048', // Validate each image
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
    ]);

    // Process each uploaded image
    $imageUrls = [];
    foreach ($validatedData['img'] as $image) {
        $imagePath = $image->store('products', 'public'); // Save image to 'storage/app/public/products'
        $imageUrls[] = asset('storage/' . $imagePath); // Generate the full URL
    }
    // Save image URLs as JSON in the database
    $validatedData['img'] = json_encode($imageUrls);

    // Create the product
    $product = Product::create($validatedData);

    return response()->json([
        'message' => 'Product created successfully',
        'data' => $product,
    ], 201);
}
    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        $product->load('category'); // Eager load category

        return response()->json([
            'message' => 'Product fetched successfully',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'img' => 'sometimes|required|array',
            'img.*' => 'sometimes|required|string|url', // If you're updating URLs
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        // Update the product
        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}

// namespace App\Http\Controllers;

// use App\Models\Product;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage; // Correctly importing the Storage facade

// class ProductController extends Controller
// {
//     /**
//      * Fetch all products with their associated category.
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function index()
//     {
//         $products = Product::with('category')->get();
        
//         return response()->json([
//             'message' => 'Products fetched successfully',
//             'data' => $products,
//         ]);
//     }

//     /**
//      * Store a newly created product.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\JsonResponse
//      */
//     // public function store(Request $request)
//     // {
//     //     // Validate the request
//     //     $validatedData = $request->validate([
//     //         'name' => 'required|string|max:255',
//     //         'img' => 'required|array', // Ensure 'img' is an array
//     //         'img.*' => 'required|string', // Each element in 'img' must be a valid URL
//     //         'price' => 'required|numeric|min:0',
//     //         'category_id' => 'required|exists:categories,id',
//     //     ]);

//     //     // Create the product
//     //     $product = Product::create($validatedData);

//     //     return response()->json([
//     //         'message' => 'Product created successfully',
//     //         'data' => $product,
//     //     ], 201);
//     // }
//     // public function store(Request $request)
//     // {
//     //     // Validate the request
//     //     $validatedData = $request->validate([
//     //         'name' => 'required|string|max:255',
//     //         'img' => 'required|array', // Ensure 'img' is an array
//     //         'img.*' => 'required|string', // Each element in 'img' must be a valid URL
//     //         'price' => 'required|numeric|min:0',
//     //         'category_id' => 'required|exists:categories,id',
//     //     ]);

//     //     // Create the product
//     //     $product = Product::create($validatedData);

//     //     return response()->json([
//     //         'message' => 'Product created successfully',
//     //         'data' => $product,
//     //     ], 201);
//     // }
//     public function store(Request $request)
//     {
//         // Validate the incoming request
//         $validatedData = $request->validate([
//             'name' => 'required|string|max:255',
//             'img' => 'required|array', // Ensure 'img' is an array
//             'img.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image format
//             'price' => 'required|numeric|min:0',
//             'category_id' => 'required|exists:categories,id',
//         ]);
    
//         // Create the product record
//         $product = Product::create([
//             'name' => $validatedData['name'],
//             'price' => $validatedData['price'],
//             'category_id' => $validatedData['category_id'],
//         ]);
    
//         // Handle image upload and store in the public directory
//         $imagePaths = [];
//         foreach ($validatedData['img'] as $image) {
//             // Store each image in 'public/products' and get the path
//             $imagePath = $image->store('products', 'public');
//             $imagePaths[] = $imagePath;
//         }
    
//         // Save the image paths to the product (you can store this as JSON or an array)
//         $product->img = json_encode($imagePaths);  // Store image paths as a JSON string
//         $product->save();
    
//         // Return a response with the product data, including image URLs
//         return response()->json([
//             'message' => 'Product created successfully',
//             'data' => [
//                 'id' => $product->id,
//                 'name' => $product->name,
//                 'price' => $product->price,
//                 'category_id' => $product->category_id,
//                 'images' => array_map(function ($path) {
//                     return url('storage/' . $path);  // Generate the public URL
//                 }, $imagePaths),
//             ],
//         ], 201);
//     }
    
//     /**
//      * Display the specified product.
//      *
//      * @param  \App\Models\Product  $product
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function show(Product $product)
//     {
//         // Ensure category relationship is included
//         $product->load('category');

//         return response()->json([
//             'message' => 'Product fetched successfully',
//             'data' => $product,
//         ]);
//     }

//     /**
//      * Update the specified product.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  \App\Models\Product  $product
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function update(Request $request, Product $product)
//     {
//         // Validate incoming request data
//         $validatedData = $request->validate([
//             'name' => 'sometimes|required|string|max:255',
//             'img' => 'sometimes|required|array',
//             'img.*' => 'sometimes|required|string|url',
//             'price' => 'sometimes|required|numeric|min:0',
//             'category_id' => 'sometimes|required|exists:categories,id',
//         ]);

//         // Update the product
//         $product->update($validatedData);

//         return response()->json([
//             'message' => 'Product updated successfully',
//             'data' => $product,
//         ]);
//     }

//     /**
//      * Remove the specified product.
//      *
//      * @param  \App\Models\Product  $product
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function destroy(Product $product)
//     {
//         $product->delete();

//         return response()->json([
//             'message' => 'Product deleted successfully',
//         ]);
//     }
// }

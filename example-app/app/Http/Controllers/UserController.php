<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;  // Add this line
use Illuminate\Support\Facades\DB;  // Add this line for DB facade

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    // Register a new user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'location' => 'nullable|string',
            'type' => 'required|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Token generation
        $verificationToken = Str::random(60);

        // Store the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'location' => $validatedData['location'] ?? null,
            'type' => $validatedData['type'],
            'img' => $path ?? null,
            'verification_token' => $verificationToken,  // Save the token
        ]);

            // $user = User::create([
            //     'name' => $validatedData['name'],
            //     'email' => $validatedData['email'],
            //     'password' => bcrypt($validatedData['password']),
            //     'location' => $validatedData['location'] ?? null,
            //     'type' => $validatedData['type'],
            //     'img' => $path ?? null,
            //     'verification_token' => $verificationToken, // Ensure token is saved
            // ]);


        // Log the verification token for debugging
        Log::info('Verification token: ' . $verificationToken);  // Now the Log class is available
        Log::info('User: ' . $user);  // Now the Log class is available

        // Send the verification email
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationToken));

        return response()->json(['message' => 'User registered successfully. Please verify your email.', 'data' => $user ,"ToKen " => $verificationToken], 201);
    }
    

    // Verify email
    public function verifyEmail(Request $request)
    {
        // Retrieve the token from the query string
        $token = $request->query('token');
    // dd($request);
        // Debugging: Check if the token is present in the request
        // dd($token);  // Check if the token is being passed
    
        // If no token is provided, return an error response
        if (!$token) {
            return response()->json(['message' => 'Token is required'], 400);
        }
    
        // Retrieve the user associated with the token
        $user = User::where('verification_token', $token)->first();
    
        // Debugging: Check if a user is found
        // dd($user);  // Check if a user is found with the token
    
        if (!$user) {
            return response()->json(['message' => 'Invalid or expired verification token'], 400);
            // Update the user's email_verified_at and reset the token
            
        }
    
        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);
    
        
        return response()->json(['message' => 'Email verified successfully'], 200);
    }
    

    
    
    


    // Get a user by ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // Update user details
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'location' => 'nullable|string|max:255',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'nullable|string|in:admin,seller,buyer',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('users', 'public');
            $validatedData['img'] = url(Storage::url($path));
        }

        $user->update(array_merge($validatedData, [
            'password' => isset($validatedData['password']) ? bcrypt($validatedData['password']) : $user->password,
        ]));

        return response()->json($user, 200);
    }

    // Login user
    public function findByEmailAndPassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Email not verified'], 403);
        }

        $token = base64_encode(Str::random(40));

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
// app/Http/Controllers/UserController.php

// namespace App\Http\Controllers;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Mail;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Storage;

// class UserController extends Controller
// {
//     /**
//      * Display a listing of the users.
//      */
//     public function index()
//     {
//         $users = User::all();
//         return response()->json($users, 200);
//     }

//     /**
//      * Store a newly created user in storage.
//      */
//     // public function store(Request $request)
//     // {
//     //     $validatedData = $request->validate([
//     //         'name' => 'required|string|max:255',
//     //         'email' => 'required|string|email|max:255|unique:users',
//     //         'password' => 'required|string|min:8',
//     //         'location' => 'nullable|string|max:255',
//     //         'img' => 'nullable|string|max:255',
//     //         'type' => 'required|string|in:admin,seller,buyer', // Ensure type is valid
//     //     ]);

//     //     $user = User::create([
//     //         'name' => $validatedData['name'],
//     //         'email' => $validatedData['email'],
//     //         'password' => Hash::make($validatedData['password']),
//     //         'location' => $validatedData['location'] ?? null,
//     //         'img' => $validatedData['img'] ?? null,
//     //         'type' => $validatedData['type'],  // Save the user type
//     //     ]);

//     //     return response()->json($user, 201);
//     // }
//     // public function store(Request $request)
//     // {
//     //     // Validate input
//     //     $request->validate([
//     //         'name' => 'required|string|max:255',
//     //         'email' => 'required|string|email|max:255|unique:users',
//     //         'password' => 'required|string|min:6',
//     //         'location' => 'nullable|string',
//     //         'type' => 'required|string',
//     //         'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
//     //     ]);
        
//     //     // Store the image file if exists
//     //     if ($request->hasFile('img')) {
//     //         $path = $request->file('img')->store('subcategories', 'public');
//     //         $validatedData['img'] = url(Storage::url($path)); // Prepend the full URL
//     //     }

//     //     // Create user
//     //     $user = User::create([
//     //         'name' => $request->name,
//     //         'email' => $request->email,
//     //         'password' => bcrypt($request->password),
//     //         'location' => $request->location,
//     //         'type' => $request->type,
//     //         'img' => $path, // Save the path of the image
//     //     ]);
    
//     //     return response()->json(['message' => 'User registered successfully', 'data' => $user], 201);
//     // }

// public function store(Request $request)
// {
//     $validatedData = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|string|email|max:255|unique:users',
//         'password' => 'required|string|min:6',
//         'location' => 'nullable|string',
//         'type' => 'required|string',
//         'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//     ]);

//     if ($request->hasFile('img')) {
//         $path = $request->file('img')->store('subcategories', 'public');
//         $validatedData['img'] = url(Storage::url($path));
//     }

//     $verificationToken = Str::random(60);

//     $user = User::create([
//         'name' => $validatedData['name'],
//         'email' => $validatedData['email'],
//         'password' => bcrypt($validatedData['password']),
//         'location' => $validatedData['location'] ?? null,
//         'type' => $validatedData['type'],
//         'img' => $path ?? null,
//         'verification_token' => $verificationToken,
//     ]);

//     // Send verification email
//     Mail::to($user->email)->send(new \App\Mail\VerifyEmail($user));

//     return response()->json(['message' => 'User registered successfully. Please verify your email.', 'data' => $user], 201);
// }
// public function verifyEmail(Request $request, $token)
// {
//     $user = User::where('verification_token', $token)->first();

//     if (!$user) {
//         return response()->json(['message' => 'Invalid or expired verification token'], 400);
//     }

//     $user->update([
//         'email_verified_at' => now(),
//         'verification_token' => null,
//     ]);

//     return response()->json(['message' => 'Email verified successfully'], 200);
// }


//     /**
//      * Display the specified user.
//      */
//     public function show($id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         return response()->json($user, 200);
//     }

//     /**
//      * Update the specified user in storage.
//      */
//     public function update(Request $request, $id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         $validatedData = $request->validate([
//             'name' => 'nullable|string|max:255',
//             'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
//             'password' => 'nullable|string|min:8',
//             'location' => 'nullable|string|max:255',
//             'img' => 'nullable|string|max:255',
//             'type' => 'nullable|string|in:admin,seller,buyer', // Validate user type
//         ]);

//         $user->update([
//             'name' => $validatedData['name'] ?? $user->name,
//             'email' => $validatedData['email'] ?? $user->email,
//             'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
//             'location' => $validatedData['location'] ?? $user->location,
//             'img' => $validatedData['img'] ?? $user->img,
//             'type' => $validatedData['type'] ?? $user->type,  // Update the user type if provided
//         ]);

//         return response()->json($user, 200);
//     }

//     /**
//      * Find a user by email and password.
//      */
//     // public function findByEmailAndPassword(Request $request)
//     // {
//     //     $validatedData = $request->validate([
//     //         'email' => 'required|string|email|max:255',
//     //         'password' => 'required|string|min:8',
//     //     ]);

//     //     $user = User::where('email', $validatedData['email'])->first();

//     //     if (!$user || !Hash::check($validatedData['password'], $user->password)) {
//     //         return response()->json(['message' => 'Invalid credentials'], 401);
//     //     }

//     //     $token = $user->createToken('YourAppName')->plainTextToken;

//     //     return response()->json([
//     //         'user' => $user,
//     //         'token' => $token
//     //     ], 200);
//     // }
//     public function findByEmailAndPassword(Request $request)
//     {
//         $validatedData = $request->validate([
//             'email' => 'required|string|email|max:255',
//             'password' => 'required|string|min:6',
//         ]);
    
//         $user = User::where('email', $validatedData['email'])->first();
    
//         if (!$user || !Hash::check($validatedData['password'], $user->password)) {
//             return response()->json(['message' => 'Invalid credentials'], 401);
//         }
    
//         if (!$user->email_verified_at) {
//             return response()->json(['message' => 'Email not verified'], 403);
//         }
    
//         $token = base64_encode(Str::random(40)); // Or use your preferred token generation logic
    
//         return response()->json([
//             'user' => $user,
//             'token' => $token,
//         ], 200);
//     }
    
//     /**
//      * Remove the specified user from storage.
//      */
//     public function destroy($id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         $user->delete();

//         return response()->json(['message' => 'User deleted successfully'], 200);
//     }
// }

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;

// class UserController extends Controller
// {
//     /**
//      * Display a listing of the users.
//      */
//     public function index()
//     {
//         $users = User::all();
//         return response()->json($users, 200);
//     }

//     /**
//      * Store a newly created user in storage.
//      */
//     public function store(Request $request)
//     {
//         $validatedData = $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8',
//             'location' => 'nullable|string|max:255',
//             'img' => 'nullable|string|max:255',
//         ]);

//         $user = User::create([
//             'name' => $validatedData['name'],
//             'email' => $validatedData['email'],
//             'password' => Hash::make($validatedData['password']),
//             'location' => $validatedData['location'] ?? null,
//             'img' => $validatedData['img'] ?? null,
//         ]);

//         return response()->json($user, 201);
//     }

//     /**
//      * Display the specified user.
//      */
//     public function show($id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         return response()->json($user, 200);
//     }

//     /**
//      * Update the specified user in storage.
//      */
//     public function update(Request $request, $id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         $validatedData = $request->validate([
//             'name' => 'nullable|string|max:255',
//             'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
//             'password' => 'nullable|string|min:8',
//             'location' => 'nullable|string|max:255',
//             'img' => 'nullable|string|max:255',
//         ]);

//         $user->update([
//             'name' => $validatedData['name'] ?? $user->name,
//             'email' => $validatedData['email'] ?? $user->email,
//             'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
//             'location' => $validatedData['location'] ?? $user->location,
//             'img' => $validatedData['img'] ?? $user->img,
//         ]);

//         return response()->json($user, 200);
//     }

//     /**
//  * Find a user by email and password.
//  */
// // public function findByEmailAndPassword(Request $request)
// // {
// //     // Validate the incoming request for email and password
// //     $validatedData = $request->validate([
// //         'email' => 'required|string|email|max:255',
// //         'password' => 'required|string|min:8',
// //     ]);

// //     // Find the user by email
// //     $user = User::where('email', $validatedData['email'])->first();

// //     // Check if the user exists and if the password matches
// //     if (!$user || !Hash::check($validatedData['password'], $user->password)) {
// //         return response()->json(['message' => 'Invalid credentials'], 401);
// //     }

// //     // Return the user details if credentials are valid
// //     return response()->json($user, 200);
// // }
// public function findByEmailAndPassword(Request $request)
// {
//     // Validate the incoming request for email and password
//     $validatedData = $request->validate([
//         'email' => 'required|string|email|max:255',
//         'password' => 'required|string|min:8',
//     ]);

//     // Find the user by email
//     $user = User::where('email', $validatedData['email'])->first();

//     // Check if the user exists and if the password matches
//     if (!$user || !Hash::check($validatedData['password'], $user->password)) {
//         return response()->json(['message' => 'Invalid credentials'], 401);
//     }

//     // Generate an API token using Sanctum
//     $token = $user->createToken('YourAppName')->plainTextToken;

//     // Return the user details and the token
//     return response()->json([
//         'user' => $user,
//         'token' => $token
//     ], 200);
// }

//     /**
//      * Remove the specified user from storage.
//      */
//     public function destroy($id)
//     {
//         $user = User::find($id);

//         if (!$user) {
//             return response()->json(['message' => 'User not found'], 404);
//         }

//         $user->delete();

//         return response()->json(['message' => 'User deleted successfully'], 200);
//     }
// }

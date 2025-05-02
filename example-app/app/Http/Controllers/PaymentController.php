<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\PaymentSuccess;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Store a new payment.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         // Validate incoming request
    //         $validatedData = $request->validate([
    //             'amount' => 'required|numeric|min:0.01',
    //             'payment_method' => 'required|string',
    //             'user_id' => 'nullable|exists:users,id', // Accepting user_id if needed
    //         ]);

    //         // Create a new payment record
    //         $payment = Payment::create([
    //             'user_id' => $validatedData['user_id'] ?? null, // user_id is optional now
    //             'amount' => $validatedData['amount'],
    //             'payment_method' => $validatedData['payment_method'],
    //             'status' => 'pending',
    //         ]);

    //         // Process the payment (example placeholder logic)
    //         $payment->processPayment();

    //         // If user_id is provided and user exists, send email
    //         if ($payment->user_id) {
    //             $user = $payment->user; // Get user using the relationship defined in the Payment model
    //             Mail::to($user->email)->send(new PaymentSuccess($payment));
    //         }

    //         return response()->json([
    //             'message' => 'Payment processed successfully',
    //             'payment' => $payment,
    //         ], 201);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // Handle validation errors
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         // Log unexpected errors
    //         \Log::error('Payment processing error: ' . $e->getMessage());
    //         return response()->json(['message' => 'Internal Server Error'], 500);
    //     }
    // }
    public function store(Request $request)
{
    try {
        // Validate incoming request
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'user_id' => 'nullable|exists:users,id', // Accepting user_id if needed
        ]);

        // Create a new payment record
        $payment = Payment::create([
            'user_id' => $validatedData['user_id'] ?? null, // user_id is optional now
            'amount' => $validatedData['amount'],
            'payment_method' => $validatedData['payment_method'],
            'status' => 'pending',
        ]);

        // Process the payment (example placeholder logic)
        $payment->processPayment();

        // If user_id is provided and user exists, send email
        if ($payment->user_id) {
            $user = $payment->user; // Get user using the relationship defined in the Payment model
            Mail::to($user->email)->send(new PaymentSuccess($payment));
        }

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment,
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // Log unexpected errors
        \Log::error('Payment processing error: ' . $e->getMessage());
        return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
    }
}

    /**
     * Fetch all payments for a given user (optional).
     */
    public function index(Request $request)
    {
        // Optionally, retrieve payments by user_id if needed
        $userId = $request->query('user_id');
        $payments = Payment::when($userId, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })->get();

        return response()->json($payments);
    }
}
// namespace App\Http\Controllers;

// use App\Models\Payment;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
//   use App\Mail\PaymentSuccess;
//     use Illuminate\Support\Facades\Mail;
    
// class PaymentController extends Controller
// {
//     /**
//      * Store a new payment.
//      */
//     // public function store(Request $request)
//     // {
//     //     try {
//     //         // Validate incoming request
//     //         $validatedData = $request->validate([
//     //             'amount' => 'required|numeric|min:0.01',
//     //             'payment_method' => 'required|string',
//     //             'user_id' => 'nullable|exists:users,id', // Accepting user_id if needed
//     //         ]);

//     //         // Create a new payment record
//     //         $payment = Payment::create([
//     //             'user_id' => $validatedData['user_id'] ?? null, // user_id is optional now
//     //             'amount' => $validatedData['amount'],
//     //             'payment_method' => $validatedData['payment_method'],
//     //             'status' => 'pending',
//     //         ]);

//     //         // Optionally, process the payment if needed
//     //         $payment->processPayment();

//     //         return response()->json([
//     //             'message' => 'Payment processed successfully',
//     //             'payment' => $payment,
//     //         ], 201);
//     //     } catch (\Illuminate\Validation\ValidationException $e) {
//     //         // Handle validation errors
//     //         Log::warning('Payment validation failed: ', $e->errors());
//     //         return response()->json(['errors' => $e->errors()], 422);
//     //     } catch (\Exception $e) {
//     //         // Handle unexpected errors
//     //         Log::error('Payment processing error: ' . $e->getMessage());
//     //         return response()->json(['message' => 'Internal Server Error'], 500);
//     //     }
//     // }
  
//     public function store(Request $request)
//     {
//         try {
//             // Validate incoming request
//             $validatedData = $request->validate([
//                 'amount' => 'required|numeric|min:0.01',
//                 'payment_method' => 'required|string',
//                 'user_id' => 'nullable|exists:users,id', // Accepting user_id if needed


//             ]);
    
//             // Create a new payment record
//             $payment = Payment::create([
//                             'user_id' => $validatedData['user_id'] ?? null, // user_id is optional now
//                             'amount' => $validatedData['amount'],
//                             'payment_method' => $validatedData['payment_method'],
//                             'status' => 'pending',
//                         ]);
    
//             // Process the payment (example placeholder logic)
//             $payment->processPayment($validatedData);
    
//             // Send an email to the user about the successful payment
//             // Assuming you have the user's email address available
//             Mail::to('user@example.com')->send(new PaymentSuccess($payment));
    
//             return response()->json([
//                 'message' => 'Payment processed successfully',
//                 'payment' => $payment,
//             ], 201);
//         } catch (\Illuminate\Validation\ValidationException $e) {
//             // Handle validation errors
//             return response()->json(['errors' => $e->errors()], 422);
//         } catch (\Exception $e) {
//             // Log unexpected errors
//             \Log::error('Payment processing error: ' . $e->getMessage());
//             return response()->json(['message' => 'Internal Server Error'], 500);
//         }
//     }
    
//     /**
//      * Fetch all payments for a given user (optional).
//      */
//     public function index(Request $request)
//     {
//         // Optionally, retrieve payments by user_id if needed
//         $userId = $request->query('user_id');
//         $payments = Payment::when($userId, function ($query, $userId) {
//             return $query->where('user_id', $userId);
//         })->get();

//         return response()->json($payments);
//     }
// }

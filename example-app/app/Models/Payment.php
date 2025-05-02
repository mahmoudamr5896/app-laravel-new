<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Optional: If you want to associate payments with users
        'amount',
        'status',
        'payment_method',
        'transaction_id',
    ];

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Process payment logic.
     * Replace this with real payment gateway integration.
     */
    public function processPayment()
    {
        // Example placeholder logic for payment processing
        if (in_array($this->payment_method, ['credit_card', 'paypal'])) {
            $this->status = 'completed';
            $this->transaction_id = 'TXN123456'; // Placeholder transaction ID, replace with real
        } else {
            $this->status = 'failed';
        }
        $this->save();
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Payment extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'user_id', // Optional: If you want to associate payments with users
//         'amount',
//         'status',
//         'payment_method',
//         'transaction_id',
//     ];

//     /**
//      * Process payment logic.
//      * Replace this with real payment gateway integration.
//      */
//     public function processPayment()
//     {
//         // Example placeholder logic for payment processing
//         if (in_array($this->payment_method, ['credit_card', 'paypal'])) {
//             $this->status = 'completed';
//             $this->transaction_id = 'TXN123456'; // Placeholder transaction ID, replace with real
//         } else {
//             $this->status = 'failed';
//         }
//         $this->save();
//     }
// }

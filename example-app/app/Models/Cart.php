<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = ['user_id', 'products'];

    // Cast the 'products' column to an array when retrieving it
    protected $casts = [
        'products' => 'array', // Automatically cast JSON column to array
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

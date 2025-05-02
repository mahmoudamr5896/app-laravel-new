<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'img',
        'likes',
        'price',
        'category_id',
        'user_id', // Added user_id field to make sure it's fillable
    ];

    // Cast img field to an array when retrieving data
    protected $casts = [
        'img' => 'array',
    ];

    // Define relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// class Product extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'name',
//         'img',
//         'likes',
//         'price',
//         'category_id',

//     ];

//     // Cast img field to an array when retrieving data
//     protected $casts = [
//         'img' => 'array',
//     ];

//     // Define relationship with Category
//     public function category()
//     {
//         return $this->belongsTo(Category::class);
//     }
// }

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Product extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'name',
//         'img',
//         'likes',
//         'price',
//         'category_id',
//     ];

//     // Cast img field to an array
//     protected $casts = [
//         'img' => 'array',
//     ];
//     public function category()
//     {
//         return $this->belongsTo(Category::class, 'category_id');
//     }
// }

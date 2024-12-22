<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('img'); // Store image URLs as a text (JSON-encoded string)
            $table->integer('likes')->default(0);
            $table->decimal('price', 8, 2);
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('products', function (Blueprint $table) {
//             $table->id();
//             $table->string('name');
//             $table->json('img'); // JSON column for storing image arrays
//             $table->integer('likes')->default(0); // Corrected column name
//             $table->decimal('price', 8, 2); // Stores prices with 2 decimal points
//             $table->unsignedBigInteger('category_id'); // Foreign key to categories table
//             $table->timestamps();

//             // Define the foreign key constraint
//             $table->foreign('category_id')
//                   ->references('id')
//                   ->on('categories')
//                   ->onDelete('cascade');
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('products');
//     }
// };

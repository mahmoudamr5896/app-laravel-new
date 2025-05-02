<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key to users table (optional)
            $table->decimal('amount', 10, 2); // Payment amount with two decimal places
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending'); // Payment status
            $table->string('payment_method'); // Payment method (e.g., credit card, PayPal)
            $table->string('transaction_id')->nullable(); // Transaction ID from the payment gateway
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

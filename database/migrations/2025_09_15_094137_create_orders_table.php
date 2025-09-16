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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary Key: order id
            $table->unsignedBigInteger('user_id')->nullable(); // linked user (nullable for guest)
            $table->decimal('total_price', 10, 2); // total order amount
            $table->string('status')->default('pending'); // order status: pending/completed/cancelled
            $table->string('payment_method'); // e.g., cod / online
            $table->text('shipping_address'); // customer shipping address
            $table->timestamps(); // created_at & updated_at

            // Optional: Foreign key constraint if users table exists
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

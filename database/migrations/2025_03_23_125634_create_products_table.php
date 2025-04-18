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
            $table->string('product_code')->unique(); // Ensure unique product codes
            $table->string('name');
            $table->integer('price');
            $table->integer('stock')->default(1);
            $table->enum('discount_type', ['none', 'buy_one_get_one', 'buy_two_get_one', 'percentage'])->default('none');
            $table->unsignedInteger('discount_value')->nullable(); // Allow null for discount value
            $table->date('expiration_date')->nullable(); // Add expiration date
            $table->string('event_discount')->nullable(); // Add event discount
            $table->timestamps();
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

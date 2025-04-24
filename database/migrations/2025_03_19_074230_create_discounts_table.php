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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Discount name
            $table->foreignId('buy_product_id')->nullable(); // Product to buy
            $table->integer('buy_quantity'); // Quantity to buy
            $table->foreignId('free_product_id')->nullable(); // Free product
            $table->integer('free_quantity')->nullable(); // Quantity of free product
            $table->enum('discount_type', ['buy_x_get_y', 'percentage'])->default('buy_x_get_y'); // Discount type
            $table->integer('discount_percentage')->nullable(); // Percentage discount (if applicable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
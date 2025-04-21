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
            $table->string('image')->nullable();
            $table->string('product_code')->unique();
            $table->unsignedBigInteger('category_id')->nullable(); // Ensure this is unsigned
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->string('name');
            $table->integer('price');
            $table->integer('stock')->default(1);
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

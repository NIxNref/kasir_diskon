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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // Unique transaction code
            $table->foreignId('member_id')->nullable(); // Add member_id
            $table->integer('total_price')->default(0);
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'qris'])->default('cash'); // Add payment method
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

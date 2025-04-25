<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItems extends Model
{
    protected $fillable = ['transaction_id', 'product_id', 'quantity', 'total_price', 'discount_amount'];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}

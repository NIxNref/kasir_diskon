<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = ['total_price', 'payment_method'];

    public function items()
    {
        return $this->hasMany(TransactionItems::class, 'transaction_id');
    }
}

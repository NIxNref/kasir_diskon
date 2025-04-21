<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'cashier_id',
        'member_id',
        'total_price',
        'payment_method',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItems::class, 'transaction_id');
    }
    
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    protected $fillable = [
        'name',
        'buy_product_id',
        'buy_quantity',
        'free_product_id',
        'free_quantity',
        'discount_type',
        'discount_percentage',
    ];

    public function buyProduct()
    {
        return $this->belongsTo(Products::class, 'buy_product_id');
    }

    public function freeProduct()
    {
        return $this->belongsTo(Products::class, 'free_product_id');
    }
}
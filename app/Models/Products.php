<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Products extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_code',
        'name',
        'price',
        'stock',
        'discount_type',
        'discount_value',
        'expiration_date',
        'event_discount',
    ];

    protected $casts = [
        'expiration_date' => 'date', // Ensure expiration_date is cast to a Carbon date instance
    ];

    /**
     * Calculate the discounted price based on quantity, total spending, and applicable discounts.
     */
    public function getDiscountedPrice($quantity, $totalBelanja)
    {
        $hargaSebelumDiskon = $this->price * $quantity;

        // Apply expiration-based discount (e.g., 50% off if expired)
        if ($this->expiration_date && Carbon::now()->greaterThan($this->expiration_date)) {
            $hargaSebelumDiskon *= 0.5; // 50% discount for expired items
        }

        // Apply event-based discount
        if ($this->event_discount === 'thanksgiving') {
            $hargaSebelumDiskon *= 0.8; // 20% discount for Thanksgiving
        } elseif ($this->event_discount === 'ramadhan') {
            $hargaSebelumDiskon *= 0.9; // 10% discount for Ramadhan
        }

        // Apply percentage discount
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            $hargaSebelumDiskon *= (1 - ($this->discount_value / 100));
        }

        // Apply "Buy One Get One Free" discount
        elseif ($this->discount_type === 'buy_one_get_one' && $quantity >= 2) {
            $pairs = intdiv($quantity, 2); // Every 2 items, 1 is free
            $hargaSebelumDiskon = ($pairs + ($quantity % 2)) * $this->price;
        }

        // Apply "Buy Two Get One Free" discount
        elseif ($this->discount_type === 'buy_two_get_one' && $quantity >= 3) {
            $freeItems = intdiv($quantity, 3); // Every 3 items, 1 is free
            $hargaSebelumDiskon = ($quantity - $freeItems) * $this->price;
        }

        // Apply bulk discount for spending over 1 million
        if ($totalBelanja >= 1000000) {
            $hargaSebelumDiskon *= 0.95; // 5% discount for spending over 1 million
        }

        return $hargaSebelumDiskon;
    }
}

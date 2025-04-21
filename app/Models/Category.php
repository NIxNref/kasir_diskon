<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * Relationship with Products
     */
    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}

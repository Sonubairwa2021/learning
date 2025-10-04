<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'thmnal',
        'status',
    ];

     // Local Scope to get only active products
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

      // Local Scope to get expensive products
    public function scopeExpensive($query, $minPrice)
    {
        return $query->where('price', '>=', $minPrice);
    }
    public function scopePriceBetween($query, $minPrice, $maxPrice)
{
    return $query->whereBetween('price', [$minPrice, $maxPrice]);
}
}

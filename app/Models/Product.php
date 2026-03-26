<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // TAMBAHKAN 'supplier_id' di dalam array ini
    protected $fillable = [
        'category_id', 
        'supplier_id', // <--- WAJIB ADA INI BIAR DATA GAK NULL
        'sku', 
        'name', 
        'stock', 
        'min_stock', 
        'price', 
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'quantity',
        'min_stock',
        'last_restock_at',
    ];

    protected $casts = [
        'last_restock_at' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}

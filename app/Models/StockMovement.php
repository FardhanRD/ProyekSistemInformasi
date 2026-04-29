<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'product_variant_id',
        'movement_type',
        'quantity',
        'before_qty',
        'after_qty',
        'reference_type',
        'reference_id',
        'note',
        'created_by',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

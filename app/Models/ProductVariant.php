<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'master_product_id',
        'variant_name',
        'size',
        'color',
        'sku',
        'is_active',
        'initial_stock',
        'min_stock',
        'price_adjustment',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function masterProduct()
    {
        return $this->belongsTo(MasterProduct::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }

    public function activePrice()
    {
        return $this->hasOne(ProductVariantPrice::class)->where('is_active', true)->latestOfMany();
    }

    public function inventory()
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }
}

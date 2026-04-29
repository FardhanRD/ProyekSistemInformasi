<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_product_id',
        'product_variant_id',
        'discount_type',
        'discount_value',
        'is_flash_sale',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_flash_sale' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function masterProduct()
    {
        return $this->belongsTo(MasterProduct::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

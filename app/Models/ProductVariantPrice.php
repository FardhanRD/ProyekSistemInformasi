<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'base_price',
        'sale_price',
        'discount_percent',
        'flash_sale_price',
        'flash_sale_start',
        'flash_sale_end',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_start' => 'datetime',
        'flash_sale_end' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function resolveEffectivePrice(): float
    {
        $now = now();

        if (
            $this->flash_sale_price !== null
            && $this->flash_sale_start !== null
            && $this->flash_sale_end !== null
            && $now->between($this->flash_sale_start, $this->flash_sale_end)
        ) {
            return (float) $this->flash_sale_price;
        }

        if ($this->sale_price !== null) {
            return (float) $this->sale_price;
        }

        if ($this->discount_percent !== null) {
            return (float) $this->base_price - ((float) $this->base_price * ((float) $this->discount_percent / 100));
        }

        return (float) $this->base_price;
    }
}

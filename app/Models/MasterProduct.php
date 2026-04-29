<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'brand',
        'specifications',
        'gender',
        'sport_type',
        'is_active',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }
}

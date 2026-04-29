<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // PENTING: Jangan lupa import Model User

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'price', 
        'description', 
        'image', 
        'stock',      
        'category_id',
        'supplier_id',
        'metadata',
        'user_id' // TAMBAHAN: Agar kita tahu siapa penjualnya
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produk_id', 'id'); 
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->latest();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // --- TAMBAHKAN FUNGSI INI ---
    public function penjual()
    {
        // Kita asumsikan kolom di database namanya 'user_id'
        // Jika nama kolomnya 'penjual_id', ganti 'user_id' jadi 'penjual_id'
        return $this->belongsTo(User::class, 'user_id');
    }
}
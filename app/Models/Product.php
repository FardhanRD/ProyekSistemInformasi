<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class Product extends Model
{
    use HasFactory;

    // 1. Beri tahu Laravel nama tabelnya yang benar
    protected $table = 'produk';

    // 2. Beri tahu Laravel primary key-nya bukan 'id'
    protected $primaryKey = 'produk_id';

    // 3. Beri tahu Laravel nama kolom 'created_at' yang sudah diubah
    const CREATED_AT = 'penyimpanan_waktu';

    protected $fillable = [
        'name', 
        'price', 
        'description', 
        'image', 
        'stock',      
        'category_id',
        'supplier_id',
        'metadata',
        'user_id' 
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
        return $this->hasMany(Ulasan::class, 'produk_id', 'produk_id'); 
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

    public function penjual()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
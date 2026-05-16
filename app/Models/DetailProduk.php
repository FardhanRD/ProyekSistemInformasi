<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduk extends Model
{
    protected $table = 'detail_produk';
    protected $primaryKey = 'detail_produk_id';
    public $timestamps = false;
    protected $fillable = [
        'produk_id',
        'nama_produk',
        'ukuran',
        'harga',
        'stok',
        'sku',
        'berat_gram',
        'is_active'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function warna()
    {
        return $this->belongsTo(WarnaProduk::class, 'warna_id', 'warna_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'detail_produk_id', 'detail_produk_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    protected $table = 'promo';
    protected $primaryKey = 'promo_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_promo',
        'jenis',
        'produk_id',
        'detail_produk_id',
        'persen_diskon',
        'nominal_diskon',
        'stok_flash_sale',
        'mulai',
        'selesai',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
        'is_active' => 'boolean',
        'persen_diskon' => 'decimal:2',
        'nominal_diskon' => 'decimal:2',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function detailProduk(): BelongsTo
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }
}

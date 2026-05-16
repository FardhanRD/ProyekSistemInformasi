<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movement';
    protected $primaryKey = 'movement_id';
    public $timestamps = false;

    protected $fillable = [
        'detail_produk_id',
        'jenis',
        'qty',
        'stok_sebelum',
        'stok_sesudah',
        'referensi',
        'catatan',
        'dibuat_oleh',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'qty' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function detailProduk()
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }
}


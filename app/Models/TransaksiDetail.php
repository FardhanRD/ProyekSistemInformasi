<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    protected $primaryKey = 'detail_id';
    public $timestamps = false;
    protected $fillable = ['transaksi_id','detail_produk_id','nama_produk_snap','harga_snap','ukuran_snap','warna_snap','quantity','subtotal'];

    public function detailProduk()
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

    public function getProdukAttribute()
    {
        return $this->detailProduk?->produk;
    }

    public function getWarnaAttribute()
    {
        return $this->detailProduk?->warna;
    }

    public function getQtyAttribute(): int
    {
        return (int) $this->quantity;
    }

    public function getHargaSatuanAttribute(): float
    {
        return (float) $this->harga_snap;
    }
}

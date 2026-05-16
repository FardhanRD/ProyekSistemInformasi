<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingProduk extends Model
{
    protected $table = 'rating_produk';
    protected $primaryKey = 'rating_id';
    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'buyer_id',
        'transaksi_id',
        'bintang',
        'judul_ulasan',
        'isi_ulasan',
        'foto_ulasan',
        'is_verified',
        'helpful_count',
        'balasan',
        'balas_oleh',
        'balas_tanggal',
        'created_at',
    ];

    protected $casts = [
        'foto_ulasan' => 'array',
        'is_verified' => 'boolean',
        'balas_tanggal' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function penjawab()
    {
        return $this->belongsTo(Admin::class, 'balas_oleh', 'admin_id');
    }
}


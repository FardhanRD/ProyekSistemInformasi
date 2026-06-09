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
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($rating) {
            $rating->updateProductStats();
        });

        static::deleted(function ($rating) {
            $rating->updateProductStats();
        });
    }

    public function updateProductStats()
    {
        $produk = $this->produk;
        if ($produk) {
            $avg = self::where('produk_id', $this->produk_id)->avg('bintang') ?? 0.0;
            $count = self::where('produk_id', $this->produk_id)->count();
            $produk->update([
                'rata_rating' => $avg,
                'jumlah_ulasan' => $count,
            ]);
        }
    }

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


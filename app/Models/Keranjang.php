<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'keranjang_id';

    protected $fillable = ['pengguna_id', 'detail_produk_id', 'jumlah'];



    public function detail()
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

}


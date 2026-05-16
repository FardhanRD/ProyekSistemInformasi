<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarnaProduk extends Model
{
    protected $table = 'warna_produk';
    protected $primaryKey = 'warna_id';
    public $timestamps = false;

    // produk_id sudah di-drop pada migration 2026_05_10_000025.
    // Pastikan model tidak lagi mengharuskan kolom ini.
    protected $fillable = [
        'nama_warna',
        'kode_hex',
        'gambar_warna',
    ];

    // Relasi ke Produk dihapus karena skema baru: warna_produk adalah master warna global,
    // bukan relasi langsung ke produk.
}



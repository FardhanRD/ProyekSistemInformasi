<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GambarDetailProduk extends Model
{
    protected $table = 'gambar_detail_produk';
    protected $primaryKey = 'gambar_detail_id';
    public $timestamps = false;

    protected $fillable = [
        'detail_produk_id',
        'url_gambar',
        'alt_text',
        'urutan',
    ];

    public function detailProduk(): BelongsTo
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

    public function getUrlLengkapAttribute()
    {
        if (empty($this->url_gambar)) {
            return null;
        }

        if (str_starts_with($this->url_gambar, 'http://') || str_starts_with($this->url_gambar, 'https://')) {
            return $this->url_gambar;
        }

        return Storage::url(ltrim($this->url_gambar, '/'));
    }
}

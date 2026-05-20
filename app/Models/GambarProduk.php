<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GambarProduk extends Model
{
    protected $table = 'gambar_produk';
    protected $primaryKey = 'gambar_id';
    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'url_gambar',
        'alt_text',
        'urutan',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    protected function resolveUrlGambar(): ?string
    {
        if (empty($this->url_gambar)) {
            return null;
        }

        if (str_starts_with($this->url_gambar, 'http://') || str_starts_with($this->url_gambar, 'https://')) {
            return $this->url_gambar;
        }

        return Storage::url(ltrim($this->url_gambar, '/'));
    }

    public function getUrlLengkapAttribute()
    {
        return $this->resolveUrlGambar();
    }

    public function getUrlSafeAttribute()
    {
        return $this->resolveUrlGambar();
    }
}


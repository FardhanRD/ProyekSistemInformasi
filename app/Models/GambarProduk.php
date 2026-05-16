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

    public function getUrlLengkapAttribute()
    {
        // Check if url_gambar is already an external URL
        if (str_starts_with($this->url_gambar, 'http://') || str_starts_with($this->url_gambar, 'https://')) {
            return $this->url_gambar;
        }
        
        // Otherwise, treat it as a local storage path
        return Storage::url($this->url_gambar);
    }
}


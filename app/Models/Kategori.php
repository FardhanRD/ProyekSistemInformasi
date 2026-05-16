<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'slug',
        'parent_id',
        'level',
        'urutan',
        'is_active',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'parent_id', 'kategori_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Kategori::class, 'parent_id', 'kategori_id');
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id', 'kategori_id');
    }

    // Optional helpers untuk kebutuhan UI master product
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}


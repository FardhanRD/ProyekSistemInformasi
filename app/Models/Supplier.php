<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'supplier_id';
    public $timestamps = true;

    protected $fillable = [
        'pengguna_id',
        'nama_toko',
        'nama_owner',
        'kategori_supplier',
        'no_telepon',
        'email',
        'alamat_toko',
        'foto_toko',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'supplier_id', 'supplier_id');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', 1);
    }

    public function scopeActive($query)
    {
        // di skema wajib supplier tidak ada is_active, jadi gunakan is_verified
        return $query->where('is_verified', 1);
    }
}


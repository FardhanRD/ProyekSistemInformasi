<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

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

    public static function ownerColumn(): string
    {
        return Schema::hasColumn('keranjang', 'pengguna_id') ? 'pengguna_id' : 'user_id';
    }

    public static function resolveOwnerId($user)
    {
        if (! $user) return null;
        if (! empty($user->pengguna_id)) return $user->pengguna_id;
        return $user->id ?? null;
    }

}


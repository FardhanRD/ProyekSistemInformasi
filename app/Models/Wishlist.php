<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Wishlist extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'wishlist_id';
    public $timestamps = false;

    protected $fillable = ['pengguna_id', 'produk_id'];

    public static function ownerColumn(): string
    {
        return 'pengguna_id';
    }

    public static function resolveOwnerId($user): ?int
    {
        if (! $user) {
            return null;
        }

        // Resolve Pengguna primary key for wishlist ownership.
        if (! empty($user->pengguna_id)) {
            return (int) $user->pengguna_id;
        }

        return null;
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }
}


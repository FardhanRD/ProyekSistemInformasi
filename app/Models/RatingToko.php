<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingToko extends Model
{
    protected $table = 'rating_toko';
    protected $primaryKey = 'rating_toko_id';
    public $timestamps = false;

    protected $fillable = ['supplier_id', 'buyer_id', 'kategori', 'pelayanan', 'aplikasi', 'bintang', 'komentar'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }
}


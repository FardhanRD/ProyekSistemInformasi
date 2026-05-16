<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model
{
    protected $table = 'ekspedisi';
    protected $primaryKey = 'ekspedisi_id';
    protected $fillable = ['nama_ekspedisi','jenis_layanan','estimasi_hari','ongkir_flat','ongkir_per_km','logo_url','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'ongkir_flat' => 'decimal:2',
        'ongkir_per_km' => 'decimal:2',
    ];
}

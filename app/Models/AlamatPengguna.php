<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlamatPengguna extends Model
{
    protected $table = 'alamat_pengguna';
    protected $primaryKey = 'alamat_id';
    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'label',
        'nama_penerima',
        'no_telepon',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'kode_pos',
        'alamat_lengkap',
        'is_utama',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }
}


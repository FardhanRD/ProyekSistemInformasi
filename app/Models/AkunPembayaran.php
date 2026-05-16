<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunPembayaran extends Model
{
    use HasFactory;

    protected $table = 'akun_pembayaran';
    protected $primaryKey = 'akun_pembayaran_id';

    protected $fillable = [
        'pengguna_id',
        'metode_id',
        'nomor_akun',
        'nama_akun',
        'is_active',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_id', 'metode_id');
    }
}
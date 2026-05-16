<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'pembayaran_id';
    public $timestamps = false;

    protected $fillable = [
        'transaksi_id',
        'metode_id',
        'jumlah_pembayaran',
        'status_pembayaran',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'expired_at',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function metode()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_id', 'metode_id');
    }
}


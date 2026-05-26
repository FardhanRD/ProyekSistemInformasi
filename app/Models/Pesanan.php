<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'pesanan_id';
    public $timestamps = false;

    protected $fillable = [
        'transaksi_id',
        'ekspedisi_id',
        'no_resi',
        'status_pesanan',
        'alamat_pengiriman',
        'foto_bukti',
        'waktu_diambil',
        'estimasi_tiba',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'ekspedisi_id', 'ekspedisi_id');
    }

    public function trackingLogs()
    {
        return $this->hasMany(TrackingLog::class, 'pesanan_id', 'pesanan_id');
    }

    public function trackingLog()
    {
        return $this->hasMany(TrackingLog::class, 'pesanan_id', 'pesanan_id');
    }
}


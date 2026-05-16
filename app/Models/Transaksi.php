<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengguna;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'alamat_id',
        'ekspedisi_id',
        'voucher_id',
        'kode_transaksi',
        'subtotal',
        'diskon_voucher',
        'ongkos_kirim',
        'total_harga',
        'status',
        'catatan_buyer',
        'tanggal',
    ];

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'transaksi_id');
    }

    public function alamat()
    {
        return $this->belongsTo(AlamatPengguna::class, 'alamat_id', 'alamat_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

    public function buyer()
    {
        return $this->pengguna();
    }

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'ekspedisi_id', 'ekspedisi_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'transaksi_id', 'transaksi_id');
    }

    public function pesanan()
    {
        return $this->hasOne(Pesanan::class, 'transaksi_id', 'transaksi_id');
    }
}


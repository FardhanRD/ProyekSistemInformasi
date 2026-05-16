<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingLog extends Model
{
    protected $table = 'tracking_log';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = ['pesanan_id', 'status', 'deskripsi', 'lokasi', 'waktu_update'];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function getWaktuAttribute()
    {
        return $this->waktu_update ? \Carbon\Carbon::parse($this->waktu_update) : null;
    }

    public function getCatatanAttribute(): ?string
    {
        return $this->deskripsi;
    }
}


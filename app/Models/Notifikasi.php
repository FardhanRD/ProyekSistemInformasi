<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';
    public    $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'judul',
        'pesan',
        'jenis',
        'url_redirect',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(
            User::class,
            'pengguna_id',
            'pengguna_id'
        );
    }
}

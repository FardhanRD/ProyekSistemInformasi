<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function keranjangItems()
    {
        return $this->hasMany(\App\Models\KeranjangItem::class, 'pembeli_id');
    }
    public function ulasan()
    {
        return $this->hasMany(\App\Models\Ulasan::class, 'user_id');
    }
    public function favorit()
    {
        return $this->hasMany(\App\Models\Favorit::class, 'pembeli_id');
    }
    public function pembayaran()
    {
        return $this->hasMany(\App\Models\Pembayaran::class, 'pembeli_id');
    }
    public function alamat()
    {
        return $this->hasMany(\App\Models\Alamat::class, 'pembeli_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
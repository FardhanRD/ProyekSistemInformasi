<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'pengguna_id';

    protected $fillable = [
        'nama_pengguna',
        'username',
        'email',
        'no_telepon',
        'sandi',
        'foto_profil',
        'jenis_kelamin',
        'tanggal_lahir',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'sandi',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->nama_pengguna;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama_pengguna'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->sandi;
    }

    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['sandi'] = $value;
    }

    public function getAuthPasswordName(): string
    {
        return 'sandi';
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function getPenggunaIdAttribute()
    {
        return $this->attributes['pengguna_id'] ?? $this->id;
    }

    public function buyer(): HasOne
    {
        return $this->hasOne(Buyer::class, 'pengguna_id', 'pengguna_id');
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'pengguna_id', 'pengguna_id');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'pengguna_id', 'pengguna_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(AlamatPengguna::class, 'pengguna_id', 'pengguna_id');
    }
}


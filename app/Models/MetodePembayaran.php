<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $table = 'metode_pembayaran';
    protected $primaryKey = 'metode_id';
    protected $fillable = ['metode','jenis','logo_url','instruksi','is_active'];

    public function getNamaMetodeAttribute(): ?string
    {
        return $this->metode;
    }

    public function getJenisMetodeAttribute(): ?string
    {
        return $this->jenis;
    }
}

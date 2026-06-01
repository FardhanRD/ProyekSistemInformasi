<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $primaryKey = 'voucher_id';
    protected $fillable = ['kode_voucher','nama_voucher','deskripsi','jenis_diskon','nilai_diskon','min_belanja','maks_diskon','kuota','kuota_terpakai','berlaku_mulai','berlaku_sampai','is_active'];
    public $timestamps = false;

    public function voucherKlaim(): HasMany
    {
        return $this->hasMany(VoucherKlaim::class, 'voucher_id', 'voucher_id');
    }

    public function getMinPembelianAttribute()
    {
        return $this->attributes['min_belanja'] ?? null;
    }

    public function getMaxDiskonAttribute()
    {
        return $this->attributes['maks_diskon'] ?? null;
    }

    public function getExpiredAtAttribute()
    {
        return $this->attributes['berlaku_sampai'] ?? null;
    }
}

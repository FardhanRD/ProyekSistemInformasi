<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $primaryKey = 'voucher_id';
    protected $fillable = ['kode_voucher','nama_voucher','deskripsi','jenis_diskon','nilai_diskon','min_belanja','maks_diskon','kuota','kuota_terpakai','berlaku_mulai','berlaku_sampai','is_active'];
    public $timestamps = false;
}

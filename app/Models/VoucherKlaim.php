<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherKlaim extends Model
{
    protected $table = 'voucher_klaim';
    protected $primaryKey = 'klaim_id';
    public $timestamps = false;

    protected $fillable = [
        'voucher_id',
        'buyer_id',
        'status',
        'diklaim_at',
        'digunakan_at',
    ];

    protected $casts = [
        'diklaim_at' => 'datetime',
        'digunakan_at' => 'datetime',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }
}
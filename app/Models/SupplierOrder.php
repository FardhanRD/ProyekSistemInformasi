<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    protected $table = 'supplier_order';
    protected $primaryKey = 'supplier_order_id';
    public $timestamps = false;

    protected $fillable = [
        'supplier_id',
        'admin_id',
        'kode_order',
        'total_item',
        'total_harga',
        'status',
        'catatan',
        'tanggal_order',
        'tanggal_diterima',
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
        'tanggal_diterima' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SupplierOrderDetail::class, 'supplier_order_id', 'supplier_order_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(SupplierOrderDetail::class, 'supplier_order_id', 'supplier_order_id');
    }
}

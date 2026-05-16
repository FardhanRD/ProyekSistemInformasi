<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrderDetail extends Model
{
    protected $table = 'supplier_order_detail';
    protected $primaryKey = 'sod_id';
    public $timestamps = false;

    protected $fillable = [
        'supplier_order_id',
        'detail_produk_id',
        'qty',
        'harga_beli',
        'subtotal',
    ];

    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id', 'supplier_order_id');
    }

    public function detailProduk(): BelongsTo
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }
}

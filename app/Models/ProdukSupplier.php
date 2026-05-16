<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukSupplier extends Model
{
    protected $table = 'produk_supplier';
    protected $primaryKey = 'produk_supplier_id';
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'supplier_id',
        'produk_id',
        'harga_modal',
        'catatan'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}


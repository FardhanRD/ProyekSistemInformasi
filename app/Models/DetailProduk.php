<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduk extends Model
{
    protected $table = 'detail_produk';
    protected $primaryKey = 'detail_produk_id';
    public $timestamps = false;
    protected $fillable = [
        'produk_id',
        'warna_id',
        'nama_produk',
        'ukuran',
        'harga',
        'stok',
        'sku',
        'berat_gram',
        'is_active'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function warna()
    {
        return $this->belongsTo(WarnaProduk::class, 'warna_id', 'warna_id');
    }

    public function gambarDetail()
    {
        return $this->hasMany(GambarDetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'detail_produk_id', 'detail_produk_id');
    }

    public function getPromoAktifAttribute()
    {
        $now = \Carbon\Carbon::now();
        // check specific variant promo first
        $promo = \App\Models\Promo::where('is_active', 1)
            ->where('detail_produk_id', $this->detail_produk_id)
            ->where('mulai', '<=', $now)
            ->where('selesai', '>=', $now)
            ->first();

        if (!$promo) {
            // check product-wide promo
            $promo = \App\Models\Promo::where('is_active', 1)
                ->where('produk_id', $this->produk_id)
                ->whereNull('detail_produk_id')
                ->where('mulai', '<=', $now)
                ->where('selesai', '>=', $now)
                ->first();
        }

        return $promo;
    }

    public function getHargaEfektifAttribute()
    {
        $promo = $this->promo_aktif;
        if ($promo) {
            $originalPrice = (float) $this->harga;
            $nominal = (float) ($promo->nominal_diskon ?? 0);
            if ($nominal <= 0 && !empty($promo->persen_diskon)) {
                $nominal = $originalPrice * ((float) $promo->persen_diskon) / 100;
            }
            return max(0.0, $originalPrice - $nominal);
        }
        return (float) $this->harga;
    }
}

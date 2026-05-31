<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengguna;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'alamat_id',
        'ekspedisi_id',
        'voucher_id',
        'kode_transaksi',
        'subtotal',
        'diskon_voucher',
        'ongkos_kirim',
        'total_harga',
        'status',
        'catatan_buyer',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'transaksi_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'transaksi_id');
    }

    public function alamat()
    {
        return $this->belongsTo(AlamatPengguna::class, 'alamat_id', 'alamat_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

    public function buyer()
    {
        return $this->pengguna();
    }

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'ekspedisi_id', 'ekspedisi_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'transaksi_id', 'transaksi_id');
    }

    public function pesanan()
    {
        return $this->hasOne(Pesanan::class, 'transaksi_id', 'transaksi_id');
    }

    public static function cancelExpired($pengguna_id = null)
    {
        $query = self::where('status', 'menunggu_pembayaran')
            ->whereHas('pembayaran', function ($q) {
                $q->where('expired_at', '<', now());
            });
            
        if ($pengguna_id) {
            $query->where('pengguna_id', $pengguna_id);
        }
        
        $expiredTransactions = $query->get();
        
        foreach ($expiredTransactions as $transaksi) {
            \DB::transaction(function () use ($transaksi) {
                $transaksi->update(['status' => 'dibatalkan']);
                if ($transaksi->pembayaran) {
                    $transaksi->pembayaran->update(['status_pembayaran' => 'expired']);
                }

                // Restore stock
                foreach ($transaksi->details as $detail) {
                    $detailProduk = $detail->detailProduk;
                    if ($detailProduk) {
                        $stokSebelum = (int) $detailProduk->stok;
                        $detailProduk->increment('stok', $detail->quantity);
                        $stokSesudah = (int) $detailProduk->stok;
                        
                        // Log stock movement if table exists
                        if (\Schema::hasTable('stock_movement')) {
                            \App\Models\StockMovement::create([
                                'detail_produk_id' => $detail->detail_produk_id,
                                'jenis' => 'in',
                                'qty' => $detail->quantity,
                                'stok_sebelum' => $stokSebelum,
                                'stok_sesudah' => $stokSesudah,
                                'referensi' => $transaksi->kode_transaksi,
                                'catatan' => 'Restorasi stok dari pesanan kadaluarsa otomatis',
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            });
        }
    }
}


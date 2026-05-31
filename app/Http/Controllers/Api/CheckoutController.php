<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use App\Models\MetodePembayaran;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Keranjang;
use App\Models\Voucher;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    public function options()
    {
        $shipping = Ekspedisi::query()
            ->selectRaw('MIN(ekspedisi_id) as ekspedisi_id, nama_ekspedisi, jenis_layanan, estimasi_hari, MIN(ongkir_flat) as ongkir_flat, MIN(ongkir_per_km) as ongkir_per_km, MAX(logo_url) as logo_url')
            ->where('is_active', 1)
            ->groupBy('nama_ekspedisi', 'jenis_layanan', 'estimasi_hari')
            ->orderBy('nama_ekspedisi')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->ekspedisi_id,
                    'name' => $e->nama_ekspedisi . ' (' . $e->jenis_layanan . ')',
                    'price' => (int) $e->ongkir_flat,
                    'est' => $e->estimasi_hari,
                    'per_km' => (int) $e->ongkir_per_km,
                    'logo_url' => $e->logo_url ? url('storage/' . $e->logo_url) : null,
                ];
            });

        $payment = MetodePembayaran::where('is_active', 1)->get()->map(function ($p) {
            return [
                'id' => $p->metode_id,
                'type' => $p->jenis,
                'name' => $p->metode,
                'instruction' => $p->instruksi,
                'logo_url' => $p->logo_url ? url('storage/' . $p->logo_url) : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'shipping' => $shipping,
            'payment' => $payment
        ], 200);
    }

    public function process(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'alamat_id' => 'required|integer|exists:alamat_pengguna,alamat_id',
            'ekspedisi_id' => 'required|integer|exists:ekspedisi,ekspedisi_id',
            'metode_id' => 'required|integer|exists:metode_pembayaran,metode_id',
            'cart_ids' => 'nullable|array',
            'cart_ids.*' => 'integer',
            'detail_produk_id' => 'nullable|integer|exists:detail_produk,detail_produk_id',
            'jumlah' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $cartItems = collect();
            
            if ($request->has('cart_ids') && !empty($request->cart_ids)) {
                $cartItems = Keranjang::with(['detail.produk.images'])
                    ->where('pengguna_id', $user->pengguna_id)
                    ->whereIn('keranjang_id', $request->cart_ids)
                    ->get();
            } elseif ($request->has('detail_produk_id')) {
                // Direct buy (Buy Now)
                $detail = DetailProduk::with('produk.images')->findOrFail($request->detail_produk_id);
                $qty = $request->input('jumlah', 1);
                
                // Mock a cart-like object structure for processing
                $mockItem = new \stdClass();
                $mockItem->keranjang_id = null;
                $mockItem->jumlah = $qty;
                
                $mockDetail = new \stdClass();
                $mockDetail->detail_produk_id = $detail->detail_produk_id;
                $mockDetail->harga = $detail->harga;
                $mockDetail->ukuran = $detail->ukuran;
                $mockDetail->nama_produk = $detail->produk->nama_produk;
                $mockItem->detail = $mockDetail;
                
                $cartItems->push($mockItem);
            }

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Keranjang kosong atau item tidak ditemukan'], 422);
            }

            $subtotal = 0;
            foreach ($cartItems as $c) {
                $subtotal += (float) $c->detail->harga * (int) $c->jumlah;
            }

            $ongkir = (float) (Ekspedisi::where('ekspedisi_id', $request->ekspedisi_id)->value('ongkir_flat') ?? 0);
            $biayaLayanan = 0;

            // Tambahkan kode unik jika metode pembayaran adalah transfer bank
            $kodeUnik = 0;
            $metodePembayaran = MetodePembayaran::find($request->metode_id);
            if ($metodePembayaran && str_contains(strtolower($metodePembayaran->jenis), 'transfer')) {
                $kodeUnik = rand(100, 999);
            }

            $discount = 0;
            $voucherId = null;

            $totalBayar = max(0, ($subtotal + $ongkir + $biayaLayanan + $kodeUnik) - $discount);
            $kode = 'INV-' . date('Ymd') . '-' . rand(100, 999);

            $trans = Transaksi::create([
                'pengguna_id' => $user->pengguna_id,
                'alamat_id' => $request->alamat_id,
                'ekspedisi_id' => $request->ekspedisi_id,
                'voucher_id' => $voucherId,
                'kode_transaksi' => $kode,
                'subtotal' => $subtotal,
                'diskon_voucher' => $discount,
                'ongkos_kirim' => $ongkir,
                'total_harga' => $totalBayar,
                'status' => 'menunggu_pembayaran',
                'tanggal' => now()
            ]);

            foreach ($cartItems as $c) {
                TransaksiDetail::create([
                    'transaksi_id' => $trans->transaksi_id,
                    'detail_produk_id' => $c->detail->detail_produk_id,
                    'nama_produk_snap' => $c->detail->nama_produk ?? $c->detail->produk->nama_produk,
                    'harga_snap' => $c->detail->harga,
                    'ukuran_snap' => $c->detail->ukuran,
                    'warna_snap' => null,
                    'quantity' => $c->jumlah,
                    'subtotal' => $c->detail->harga * $c->jumlah
                ]);
            }

            // Pembayaran
            $pembayaran = \App\Models\Pembayaran::create([
                'transaksi_id' => $trans->transaksi_id,
                'metode_id' => (int) $request->metode_id,
                'jumlah_pembayaran' => $totalBayar,
                'kode_unik' => $kodeUnik > 0 ? $kodeUnik : null,
                'status_pembayaran' => 'menunggu',
                'tanggal_pembayaran' => null,
                'expired_at' => now()->addHours(24),
            ]);

            // Pesanan
            if (Schema::hasTable('pesanan')) {
                \App\Models\Pesanan::create([
                    'transaksi_id' => $trans->transaksi_id,
                    'ekspedisi_id' => $request->ekspedisi_id,
                    'no_resi' => null,
                    'status_pesanan' => 'menunggu_konfirmasi',
                    'alamat_pengiriman' => trim(($user->addresses()->where('alamat_id', $request->alamat_id)->value('alamat_lengkap') ?? '-') . ', ' . ($user->addresses()->where('alamat_id', $request->alamat_id)->value('kota') ?? '-') . ', ' . ($user->addresses()->where('alamat_id', $request->alamat_id)->value('provinsi') ?? '-')),
                    'waktu_diambil' => null,
                    'estimasi_tiba' => now()->addDays(3)->toDateString(),
                ]);
            }

            // Hapus items dari keranjang jika via keranjang
            $cartItemIds = $cartItems->pluck('keranjang_id')->filter()->all();
            if (!empty($cartItemIds)) {
                Keranjang::where('pengguna_id', $user->pengguna_id)
                    ->whereIn('keranjang_id', $cartItemIds)
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil diproses.',
                'data' => [
                    'transaksi_id' => $trans->transaksi_id,
                    'kode_transaksi' => $trans->kode_transaksi,
                    'total_harga' => (int) $trans->total_harga,
                    'status' => $trans->status,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

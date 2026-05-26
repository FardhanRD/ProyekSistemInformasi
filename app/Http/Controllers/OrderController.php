<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Buyer;
use App\Models\RatingProduk;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');

        $buyer = $user->buyer;
        if (!$buyer) {
            return view('buyer.order.index', [
                'transaksis' => collect(),
                'orderCounts' => [],
            ]);
        }

        // Get order counts for each status
        $allStatuses = ['', 'menunggu_pembayaran', 'pembayaran_dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $orderCounts = [];
        
        foreach ($allStatuses as $status) {
            $query = Transaksi::where('pengguna_id', $user->pengguna_id);
            if ($status !== '') {
                $query->where('status', $status);
            }
            $orderCounts[$status] = $query->count();
        }

        // Get filtered orders
        $query = Transaksi::where('pengguna_id', $user->pengguna_id)
            ->with([
                'details.detailProduk.produk.gambarUtama',
                'pembayaran.metode',
                'ekspedisi'
            ]);
        
        $currentStatus = $request->input('status', '');
        if ($currentStatus !== '') {
            $query->where('status', $currentStatus);
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get();

        return view('buyer.order.index', compact('transaksis', 'orderCounts'));
    }

    public function show($kode_transaksi)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');
        $buyer = $user->buyer;
        if (! $buyer) {
            abort(403, 'Akses ditolak.');
        }

        $transaksi = Transaksi::with([
            'transaksiDetail.detailProduk.produk.gambarUtama',
            'transaksiDetail.detailProduk.warna',
            'alamat',
            'ekspedisi',
            'pembayaran.metodePembayaran',
            'pesanan.trackingLog',
            'voucher',
        ])
        ->where('kode_transaksi', $kode_transaksi)
        ->where('pengguna_id', $buyer->pengguna_id)
        ->firstOrFail();

        return view('buyer.order.detail', compact('transaksi'));
    }

    // Metode untuk rating produk dan toko akan dipindahkan ke API atau controller terpisah
    // agar lebih modular dan sesuai dengan praktik terbaik.
    // Untuk saat ini, kita akan mengasumsikan rating dilakukan melalui API atau form terpisah.
    public function ratingProduk(Request $request, $id) { /* ... */ }
    public function ratingToko(Request $request, $id) { /* ... */ }
}

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

        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();

        $query = Transaksi::with([
            'details.detailProduk.produk.images' => fn($q) => $q->where('urutan', 0),
            'pembayaran',
            'ekspedisi'
        ]);
        
        if ($buyer) {
            $query->where('pengguna_id', $user->pengguna_id);
        } else {
            $query->whereRaw('1=0'); // Return empty result
        }

        // Filter status
        $currentStatus = $request->input('status', 'all');
        if ($currentStatus !== 'all') {
            $query->where('status', $currentStatus);
        }

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        // Search kode transaksi
        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('tanggal', 'desc')->paginate(10); // Pagination 10 items per page

        return view('buyer.order.index', compact('orders', 'currentStatus'));
    }

    public function show($kode_transaksi)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');

        $transaksi = Transaksi::with([
            'details.detailProduk.produk.images' => fn($q) => $q->where('urutan', 0),
            'pembayaran.metode',
            'ekspedisi',
            'alamat',
            'pesanan.trackingLogs' => fn($q) => $q->orderBy('waktu_update', 'asc')
        ])->where('kode_transaksi', $kode_transaksi)->firstOrFail();

        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();
        if (! $buyer) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi kepemilikan transaksi
        if ($transaksi->buyer->pengguna_id !== $user->pengguna_id) {
            abort(403, 'Akses ditolak.');
        }

        // Cek apakah user sudah memberikan ulasan untuk produk di transaksi ini
        $reviewedProductIds = RatingProduk::where('buyer_id', $buyer->buyer_id)
                                ->whereIn('produk_id', $transaksi->details->pluck('detailProduk.produk_id'))
                                ->pluck('produk_id')
                                ->toArray();

        return view('buyer.order.detail', compact('transaksi', 'reviewedProductIds'));
    }

    // Metode untuk rating produk dan toko akan dipindahkan ke API atau controller terpisah
    // agar lebih modular dan sesuai dengan praktik terbaik.
    // Untuk saat ini, kita akan mengasumsikan rating dilakukan melalui API atau form terpisah.
    public function ratingProduk(Request $request, $id) { /* ... */ }
    public function ratingToko(Request $request, $id) { /* ... */ }
}

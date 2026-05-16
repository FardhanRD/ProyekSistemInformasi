<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\Pesanan;
use App\Models\TrackingLog;

class TrackingController extends Controller
{
    public function show($transaksiId)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');

        $transaksi = Transaksi::with([
            'buyer',
            'ekspedisi',
            'alamat',
            'pesanan.trackingLogs' => fn($q) => $q->orderBy('waktu_update', 'asc'),
            'details.detailProduk.produk.images' => fn($q) => $q->where('urutan', 0)
        ])->where('kode_transaksi', $transaksiId)->firstOrFail(); // Menggunakan kode_transaksi

        // Validasi kepemilikan transaksi
        if ($transaksi->buyer->pengguna_id !== $user->pengguna_id) {
            abort(403, 'Akses ditolak.');
        }

        $pesanan = null;
        $trackingLogs = collect();

        if (Schema::hasTable('pesanan')) {
            // Mengambil pesanan berdasarkan transaksi_id dari objek transaksi yang sudah dimuat
            $pesanan = $transaksi->pesanan;
            
            if ($pesanan && Schema::hasTable('tracking_log')) {
                $trackingLogs = TrackingLog::where('pesanan_id', $pesanan->pesanan_id)
                    ->orderBy('waktu_update', 'desc') // Urutkan terbaru dulu
                    ->get();
            }
        }
        

        // Get ekspedisi info
        $ekspedisi = $transaksi->ekspedisi;
        $alamatTujuan = $transaksi->alamat;

        return view('buyer.tracking.index', compact('transaksi', 'pesanan', 'trackingLogs', 'ekspedisi', 'alamatTujuan'));
    }
}

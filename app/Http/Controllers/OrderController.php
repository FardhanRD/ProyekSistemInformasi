<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Buyer;
use App\Models\RatingProduk;
use Carbon\Carbon;

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

    public function showJson($kode)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $t = Transaksi::with([
                'transaksiDetail.detailProduk.produk.gambarUtama',
                'transaksiDetail.detailProduk.warna',
                'alamat',
                'ekspedisi',
                'pembayaran.metodePembayaran',
                'pesanan',
                'voucher',
            ])
            ->where('kode_transaksi', $kode)
            ->where('pengguna_id', $user->pengguna_id)
            ->firstOrFail();

        $statusLabel = [
            'menunggu_pembayaran' => 'Belum Bayar',
            'pembayaran_dikonfirmasi' => 'Dikonfirmasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $sudahRating = RatingProduk::where('transaksi_id', $t->transaksi_id)
            ->where('buyer_id', optional($user->buyer)->buyer_id)
            ->exists();

        return response()->json([
            'kode_transaksi' => $t->kode_transaksi,
            'status' => $t->status,
            'status_label' => $statusLabel[$t->status] ?? ucfirst($t->status),
            'tanggal' => $t->tanggal ? Carbon::parse($t->tanggal)->isoFormat('D MMM YYYY, HH:mm') : '-',
            'sudah_rating' => $sudahRating,
            'items' => $t->transaksiDetail->map(fn ($d) => [
                'id' => $d->detail_id,
                'nama' => $d->nama_produk_snap,
                'gambar' => $d->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png'),
                'ukuran' => $d->ukuran_snap ?? '-',
                'warna' => $d->warna_snap ?? 'No Color',
                'qty' => $d->quantity,
                'subtotal' => (int) $d->subtotal,
            ])->values(),
            'penerima' => $t->alamat->nama_penerima ?? '-',
            'telepon' => $t->alamat->no_telepon ?? '-',
            'alamat' => trim(
                implode(', ', array_filter([
                    $t->alamat->alamat_lengkap ?? null,
                    $t->alamat->kota ?? null,
                    $t->alamat->provinsi ?? null,
                ]))
            ),
            'ekspedisi' => trim(($t->ekspedisi->nama_ekspedisi ?? '-') . ' ' . ($t->ekspedisi->jenis_layanan ?? '')),
            'resi' => $t->pesanan->no_resi ?? null,
            'estimasi' => $t->pesanan?->estimasi_tiba
                ? Carbon::parse($t->pesanan->estimasi_tiba)->isoFormat('D MMM YYYY')
                : null,
            'subtotal' => (int) $t->subtotal,
            'ongkir' => (int) $t->ongkos_kirim,
            'diskon' => (int) $t->diskon_voucher,
            'total' => (int) $t->total_harga,
            'metode_bayar' => $t->pembayaran?->metodePembayaran?->metode ?? '-',
        ]);
    }

    // Metode untuk rating produk dan toko akan dipindahkan ke API atau controller terpisah
    // agar lebih modular dan sesuai dengan praktik terbaik.
    // Untuk saat ini, kita akan mengasumsikan rating dilakukan melalui API atau form terpisah.
    public function ratingProduk(Request $request, $id) { /* ... */ }
    public function ratingToko(Request $request, $id) { /* ... */ }
}

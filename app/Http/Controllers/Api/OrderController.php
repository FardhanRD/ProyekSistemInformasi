<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TrackingLog;
use App\Models\Pesanan;
use App\Models\Buyer;
use App\Models\RatingProduk;
use App\Models\RatingToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Auto-cancel expired transactions for the user
        Transaksi::cancelExpired($user->pengguna_id);

        $query = Transaksi::with([
            'details.detailProduk.produk.images' => fn($q) => $q->orderBy('urutan', 'asc'),
            'pembayaran.metode',
            'ekspedisi'
        ])->where('pengguna_id', $user->pengguna_id);

        $status = $request->input('status');
        if ($status) {
            if ($status === 'unpaid') {
                $query->where('status', 'menunggu_pembayaran');
            } elseif ($status === 'packed') {
                $query->where('status', 'diproses');
            } elseif ($status === 'shipping') {
                $query->where('status', 'dikirim');
            } elseif ($status === 'completed') {
                $query->where('status', 'selesai');
            } elseif ($status === 'unrated') {
                $query->where('status', 'selesai')
                      ->whereNotExists(function ($subQuery) {
                          $subQuery->select(\DB::raw(1))
                                   ->from('rating_produk')
                                   ->whereRaw('rating_produk.transaksi_id = transaksi.transaksi_id');
                      });
            } elseif ($status === 'cancelled') {
                $query->where('status', 'dibatalkan');
            } else {
                $query->where('status', $status);
            }
        }

        $orders = $query->orderBy('tanggal', 'desc')->get();

        $formattedOrders = $orders->map(function ($order) {
            $firstDetail = $order->details->first();
            $firstProduct = $firstDetail ? $firstDetail->detailProduk->produk : null;
            $imageUrl = $firstProduct && $firstProduct->images->first() ? $firstProduct->images->first()->url_gambar : '';

            $akunPembayaran = null;
            if ($order->status === 'menunggu_pembayaran' && $order->pembayaran) {
                $akunPembayaran = \App\Models\AkunPembayaran::where('is_active', true)
                    ->where('metode_id', $order->pembayaran->metode_id)
                    ->whereHas('pengguna', function ($q) {
                        $q->where('role', 'admin');
                    })
                    ->first();
            }

            $expiredAtStr = null;
            if ($order->pembayaran && $order->pembayaran->expired_at) {
                $expiredAtStr = Carbon::parse($order->pembayaran->expired_at)->toIso8601String();
            }

            return [
                'transaksi_id' => $order->transaksi_id,
                'kode_transaksi' => $order->kode_transaksi,
                'status' => $order->status,
                'subtotal' => (int) $order->subtotal,
                'ongkos_kirim' => (int) $order->ongkos_kirim,
                'total_harga' => (int) $order->total_harga,
                'tanggal' => $order->tanggal,
                'pembayaran' => $order->pembayaran ? [
                    'pembayaran_id' => $order->pembayaran->pembayaran_id,
                    'jumlah_pembayaran' => (int) $order->pembayaran->jumlah_pembayaran,
                    'kode_unik' => $order->pembayaran->kode_unik ? (int) $order->pembayaran->kode_unik : 0,
                    'status_pembayaran' => $order->pembayaran->status_pembayaran,
                    'expired_at' => $expiredAtStr,
                ] : null,
                'metode_pembayaran' => $order->pembayaran && $order->pembayaran->metode ? [
                    'metode_id' => $order->pembayaran->metode->metode_id,
                    'metode' => $order->pembayaran->metode->metode,
                    'jenis' => $order->pembayaran->metode->jenis,
                ] : null,
                'akun_pembayaran' => $akunPembayaran ? [
                    'nomor_akun' => $akunPembayaran->nomor_akun,
                    'nama_akun' => $akunPembayaran->nama_akun,
                ] : null,
                'first_item' => $firstDetail ? [
                    'product_name' => $firstProduct ? $firstProduct->nama_produk : 'Produk',
                    'image_url' => $imageUrl,
                    'variation' => ($firstDetail->detailProduk->ukuran ?? '') . ($firstDetail->detailProduk->warna ? ' - ' . $firstDetail->detailProduk->warna->nama_warna : ''),
                    'price' => (int) $firstDetail->harga_satuan,
                    'qty' => (int) $firstDetail->quantity,
                ] : null,
                'other_items_count' => max(0, $order->details->count() - 1),
                'is_rated' => RatingProduk::where('transaksi_id', $order->transaksi_id)->exists(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedOrders
        ]);
    }

    public function tracking($kode_transaksi)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Auto-cancel expired transactions before fetching
        Transaksi::cancelExpired($user->pengguna_id);

        $transaksi = Transaksi::with([
            'ekspedisi',
            'alamat',
            'pesanan.trackingLogs' => fn($q) => $q->orderBy('waktu_update', 'desc'),
            'details.detailProduk.produk.images' => fn($q) => $q->orderBy('urutan', 'asc')
        ])->where('kode_transaksi', $kode_transaksi)
          ->where('pengguna_id', $user->pengguna_id)
          ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $pesanan = $transaksi->pesanan;
        $trackingLogs = collect();
        if ($pesanan && Schema::hasTable('tracking_log')) {
            $trackingLogs = TrackingLog::where('pesanan_id', $pesanan->pesanan_id)
                ->orderBy('waktu_update', 'desc')
                ->get();
        }

        $formattedLogs = $trackingLogs->map(function ($log) {
            return [
                'status' => $log->status,
                'deskripsi' => $log->deskripsi,
                'lokasi' => $log->lokasi,
                'waktu_update' => $log->waktu_update ? Carbon::parse($log->waktu_update)->toIso8601String() : null,
            ];
        });

        $formattedDetails = $transaksi->details->map(function ($detail) {
            $produk = $detail->detailProduk->produk;
            $imageUrl = $produk && $produk->images->first() ? $produk->images->first()->url_gambar : '';
            return [
                'product_id' => $produk ? $produk->produk_id : null,
                'product_name' => $produk ? $produk->nama_produk : 'Produk',
                'image_url' => $imageUrl,
                'jumlah' => (int) $detail->quantity,
                'harga_satuan' => (int) $detail->harga_satuan,
                'ukuran' => $detail->detailProduk->ukuran,
                'warna' => $detail->detailProduk->warna ? $detail->detailProduk->warna->nama_warna : null,
            ];
        });

        $estimasiTibaStr = null;
        if ($pesanan && $pesanan->estimasi_tiba) {
            $estimasiTibaStr = Carbon::parse($pesanan->estimasi_tiba)->toIso8601String();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_transaksi' => $transaksi->kode_transaksi,
                'status_transaksi' => $transaksi->status,
                'ekspedisi' => $transaksi->ekspedisi ? [
                    'nama_ekspedisi' => $transaksi->ekspedisi->nama_ekspedisi,
                    'jenis_layanan' => $transaksi->ekspedisi->jenis_layanan,
                ] : null,
                'alamat_tujuan' => $transaksi->alamat ? [
                    'nama_penerima' => $transaksi->alamat->nama_penerima,
                    'no_telepon' => $transaksi->alamat->no_telepon,
                    'alamat_lengkap' => $transaksi->alamat->alamat_lengkap,
                ] : null,
                'pesanan' => $pesanan ? [
                    'no_resi' => $pesanan->no_resi,
                    'estimasi_tiba' => $estimasiTibaStr,
                    'status' => $pesanan->status_pesanan,
                    'total_harga' => (int) $pesanan->total_harga,
                ] : null,
                'tracking_logs' => $formattedLogs,
                'details' => $formattedDetails,
                'total_harga' => (int) $transaksi->total_harga,
            ]
        ]);
    }

    public function complete($kode_transaksi)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::where('kode_transaksi', $kode_transaksi)
            ->where('pengguna_id', $user->pengguna_id)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($transaksi->status !== 'dikirim') {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak dalam pengiriman'], 422);
        }

        \DB::transaction(function () use ($transaksi) {
            $transaksi->update(['status' => 'selesai']);

            if (Schema::hasTable('pesanan')) {
                $pesanan = Pesanan::where('transaksi_id', $transaksi->transaksi_id)->first();
                if ($pesanan) {
                    $pesanan->update(['status_pesanan' => 'diterima']);

                    if (Schema::hasTable('tracking_log')) {
                        TrackingLog::create([
                            'pesanan_id' => $pesanan->pesanan_id,
                            'status' => 'Pesanan Selesai',
                            'deskripsi' => 'Pesanan telah diterima oleh pembeli. Transaksi selesai.',
                            'lokasi' => 'Alamat Tujuan',
                            'waktu_update' => now(),
                        ]);
                    }
                }
            }

            if (Schema::hasTable('notifikasi')) {
                \App\Models\Notifikasi::create([
                    'pengguna_id' => $transaksi->pengguna_id,
                    'judul' => 'Pesanan Selesai',
                    'pesan' => "Pesanan dengan kode transaksi {$transaksi->kode_transaksi} telah selesai diterima. Terima kasih telah berbelanja di MOVR!",
                    'jenis' => 'completed',
                    'url_redirect' => 'completed',
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diselesaikan.'
        ]);
    }

    public function postRating(Request $request, $kode_transaksi)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::where('kode_transaksi', $kode_transaksi)
            ->where('pengguna_id', $user->pengguna_id)
            ->where('status', 'selesai')
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan atau belum selesai'], 404);
        }

        $request->validate([
            'produk_ratings' => 'required|array',
            'produk_ratings.*.produk_id' => 'required|integer',
            'produk_ratings.*.bintang' => 'required|integer|min:1|max:5',
            'produk_ratings.*.judul' => 'nullable|string|max:255',
            'produk_ratings.*.isi' => 'nullable|string',
            'toko_rating' => 'required|array',
            'toko_rating.bintang' => 'required|integer|min:1|max:5',
            'toko_rating.komentar' => 'nullable|string',
        ]);

        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();
        if (!$buyer) {
            $buyer = Buyer::create(['pengguna_id' => $user->pengguna_id]);
        }

        \DB::beginTransaction();
        try {
            // 1. Simpan rating produk
            foreach ($request->input('produk_ratings') as $rating) {
                if ($rating['bintang'] > 0) {
                    RatingProduk::updateOrCreate(
                        [
                            'produk_id'    => $rating['produk_id'],
                            'buyer_id'     => $buyer->buyer_id,
                            'transaksi_id' => $transaksi->transaksi_id,
                        ],
                        [
                            'bintang'      => $rating['bintang'],
                            'judul_ulasan' => $rating['judul'] ?? '',
                            'isi_ulasan'   => $rating['isi'] ?? '',
                            'is_verified'  => 1,
                            'created_at'   => now(),
                        ]
                    );
                }
            }

            // 2. Simpan rating toko
            $firstDetail = $transaksi->details->first();
            $supplierId = $firstDetail->detailProduk->produk->supplier_id;

            RatingToko::updateOrCreate(
                [
                    'supplier_id' => $supplierId,
                    'buyer_id'    => $buyer->buyer_id,
                ],
                [
                    'bintang'     => $request->input('toko_rating.bintang'),
                    'komentar'    => $request->input('toko_rating.komentar') ?? '',
                    'created_at'  => now(),
                ]
            );

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil dikirim.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

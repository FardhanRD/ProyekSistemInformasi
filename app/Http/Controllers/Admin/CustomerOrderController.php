<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Notifikasi;
use App\Models\TrackingLog;
use App\Models\StockMovement;
use App\Models\DetailProduk;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('transaksi')) {
            return view('admin.customer-order.index', [
                'orders' => collect(),
            ]);
        }

        $status = $request->get('status');
        $search = $request->get('search');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $orders = Transaksi::with(['pengguna', 'pembayaran', 'details', 'alamat.pengguna'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where('kode_transaksi', 'like', "%{$search}%")
                ->orWhereHas('pengguna', fn($sq) => $sq->where('nama_pengguna', 'like', "%{$search}%")))
            ->when($start_date, fn($q) => $q->where('tanggal', '>=', $start_date . ' 00:00:00'))
            ->when($end_date, fn($q) => $q->where('tanggal', '<=', $end_date . ' 23:59:59'))
            ->orderBy('tanggal', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customer-order.index', [
            'orders' => $orders,
            'status_filter' => $status,
            'search_filter' => $search,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function show($kode_transaksi)
    {
        $order = Transaksi::with(['pengguna', 'pembayaran', 'pembayaran.metode', 'details.detailProduk.produk', 'details.detailProduk.warna', 'alamat', 'ekspedisi', 'voucher'])
            ->where('kode_transaksi', $kode_transaksi)
            ->firstOrFail();

        $pesanan = Pesanan::where('transaksi_id', $order->transaksi_id)
            ->with(['trackingLogs'])
            ->first();

        return view('admin.customer-order.show', [
            'order' => $order,
            'pesanan' => $pesanan,
        ]);
    }

    public function verifyPayment(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        return $this->verify($request, $pembayaran->transaksi_id);
    }

    public function verify(Request $request, $id)
    {
        return $this->updatePaymentStatus($request, $id, 'berhasil');
    }

    public function reject(Request $request, $id)
    {
        return $this->updatePaymentStatus($request, $id, 'gagal');
    }

    private function updatePaymentStatus(Request $request, $id, string $status)
    {
        $transaksi = Transaksi::with(['buyer', 'pembayaran', 'pesanan'])->findOrFail($id);

        if (! $transaksi->pembayaran) {
            $message = 'Data pembayaran tidak ditemukan.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }

            return back()->with('error', $message);
        }

        $currentStatus = strtolower((string) $transaksi->pembayaran->status_pembayaran);

        if (! in_array($currentStatus, ['menunggu', 'menunggu_konfirmasi'], true)) {
            $message = 'Pembayaran sudah diproses sebelumnya.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        DB::beginTransaction();
        try {
            $transaksi->pembayaran->update([
                'status_pembayaran' => $status,
                'tanggal_pembayaran' => now(),
            ]);

            if ($status === 'berhasil') {
                // Mark transaksi as payment-confirmed so it appears in user's "Dikonfirmasi" tab
                $transaksi->update(['status' => 'pembayaran_dikonfirmasi']);

                if ($transaksi->pesanan) {
                    // Update order status to 'dikonfirmasi' so it shows under My Orders -> Dikonfirmasi
                    $transaksi->pesanan->update([
                        'status_pesanan' => 'dikonfirmasi',
                    ]);
                }

                if (Schema::hasTable('transaksi_detail') && Schema::hasTable('stock_movement')) {
                    $details = TransaksiDetail::where('transaksi_id', $transaksi->transaksi_id)->get();

                    foreach ($details as $detail) {
                        $variant = DetailProduk::find($detail->detail_produk_id);
                        if ($variant) {
                            StockMovement::create([
                                'detail_produk_id' => $variant->detail_produk_id,
                                'jenis' => 'out',
                                'qty' => $detail->qty,
                                'stok_sebelum' => $variant->stok,
                                'stok_sesudah' => max(0, $variant->stok - $detail->qty),
                                'referensi' => $transaksi->kode_transaksi,
                                'catatan' => 'Pengurangan stok dari transaksi customer',
                                'dibuat_oleh' => auth()->user()->id,
                                'created_at' => now(),
                            ]);

                            $variant->update(['stok' => max(0, $variant->stok - $detail->qty)]);
                        }
                    }
                }

                if (Schema::hasTable('pesanan') && Schema::hasTable('tracking_log')) {
                    $pesanan = Pesanan::where('transaksi_id', $transaksi->transaksi_id)->first();
                    if ($pesanan) {
                        TrackingLog::create([
                            'pesanan_id' => $pesanan->pesanan_id,
                            'status' => 'Pembayaran dikonfirmasi',
                            'deskripsi' => 'Pembayaran telah diverifikasi oleh admin',
                            'waktu_update' => now(),
                        ]);
                    }
                }

                $buyerId = $transaksi->buyer?->pengguna_id;
                if ($buyerId) {
                    Notifikasi::create([
                        'pengguna_id' => $buyerId,
                        'judul' => '✅ Pembayaran Dikonfirmasi',
                        'pesan' => 'Pembayaran untuk pesanan ' . $transaksi->kode_transaksi . ' telah dikonfirmasi. Pesanan sedang diproses.',
                        'jenis' => 'transaksi',
                        'url_redirect' => '/orders',
                        'is_read' => 0,
                        'created_at' => now(),
                    ]);
                }
            } else {
                $buyerId = $transaksi->buyer?->pengguna_id;
                if ($buyerId) {
                    Notifikasi::create([
                        'pengguna_id' => $buyerId,
                        'judul' => '❌ Pembayaran Ditolak',
                        'pesan' => 'Pembayaran untuk pesanan ' . $transaksi->kode_transaksi . ' ditolak. Silakan cek kembali bukti pembayaran atau hubungi admin.',
                        'jenis' => 'transaksi',
                        'url_redirect' => '/orders',
                        'is_read' => 0,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $status === 'berhasil' ? 'Pembayaran berhasil diverifikasi.' : 'Pembayaran berhasil ditolak.',
                    'status' => $status,
                ]);
            }

            return back()->with('success', $status === 'berhasil' ? 'Pembayaran berhasil diverifikasi.' : 'Pembayaran berhasil ditolak.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu_pembayaran,pembayaran_dikonfirmasi,diproses,dikirim,selesai,dibatalkan',
        ]);

        $transaksi = Transaksi::with(['pesanan', 'buyer'])->findOrFail($id);

        $statusMap = [
            'menunggu_pembayaran' => 'pembayaran_menunggu',
            'pembayaran_dikonfirmasi' => 'pembayaran_dikonfirmasi',
            'diproses' => 'pesanan_diproses',
            'dikirim' => 'pesanan_dikirim',
            'selesai' => 'pesanan_selesai',
            'dibatalkan' => 'pesanan_dibatalkan',
        ];

        $pesananStatusMap = [
            'pembayaran_dikonfirmasi' => 'menunggu_konfirmasi',
            'diproses' => 'dikemas',
            'dikirim' => 'dalam_pengiriman',
            'selesai' => 'diterima',
            'dibatalkan' => 'dibatalkan',
        ];

        $notifMap = [
            'dikirim' => [
                'judul' => '🚚 Pesanan Dikirim',
                'pesan' => 'Pesanan ' . $transaksi->kode_transaksi . ' sedang dalam pengiriman',
            ],
            'selesai' => [
                'judul' => '✅ Pesanan Selesai',
                'pesan' => 'Pesanan ' . $transaksi->kode_transaksi . ' telah selesai. Berikan rating!',
            ],
            'dibatalkan' => [
                'judul' => '❌ Pesanan Dibatalkan',
                'pesan' => 'Pesanan ' . $transaksi->kode_transaksi . ' telah dibatalkan',
            ],
        ];

        DB::beginTransaction();
        try {
            $transaksi->update([
                'status' => $statusMap[$request->status] ?? $request->status,
            ]);

            if ($transaksi->pesanan && isset($pesananStatusMap[$request->status])) {
                $transaksi->pesanan->update([
                    'status_pesanan' => $pesananStatusMap[$request->status],
                ]);

                if (Schema::hasTable('tracking_log')) {
                    TrackingLog::create([
                        'pesanan_id' => $transaksi->pesanan->pesanan_id,
                        'status' => ucfirst(str_replace('_', ' ', $request->status)),
                        'deskripsi' => 'Status diupdate oleh admin',
                        'waktu_update' => now(),
                    ]);
                }
            }

            $buyerId = $transaksi->buyer?->pengguna_id;
            if ($buyerId && isset($notifMap[$request->status])) {
                Notifikasi::create([
                    'pengguna_id' => $buyerId,
                    'judul' => $notifMap[$request->status]['judul'],
                    'pesan' => $notifMap[$request->status]['pesan'],
                    'jenis' => 'transaksi',
                    'url_redirect' => '/orders',
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diupdate',
                ]);
            }

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateResi(Request $request, $id)
    {
        $request->validate([
            'no_resi' => 'required|string|max:100',
        ]);

        $transaksi = Transaksi::with(['pesanan', 'buyer'])->findOrFail($id);

        if (! $transaksi->pesanan) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            $transaksi->pesanan->update([
                'no_resi' => $request->no_resi,
                'status_pesanan' => 'dalam_pengiriman',
            ]);

            $transaksi->update(['status' => 'pesanan_dikirim']);

            if (Schema::hasTable('tracking_log')) {
                TrackingLog::create([
                    'pesanan_id' => $transaksi->pesanan->pesanan_id,
                    'status' => 'Paket Dikirim',
                    'deskripsi' => 'Paket telah diserahkan ke kurir dengan resi: ' . $request->no_resi,
                    'waktu_update' => now(),
                ]);
            }

            $buyerId = $transaksi->buyer?->pengguna_id;
            if ($buyerId) {
                Notifikasi::create([
                    'pengguna_id' => $buyerId,
                    'judul' => '🚚 Pesanan Dikirim',
                    'pesan' => 'Pesanan ' . $transaksi->kode_transaksi . ' sedang dikirim. Resi: ' . $request->no_resi,
                    'jenis' => 'pengiriman',
                    'url_redirect' => '/tracking/' . $transaksi->kode_transaksi,
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resi berhasil disimpan',
                ]);
            }

            return redirect()->back()->with('success', 'Nomor resi berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function invoicePdf($id)
    {
        $transaksi = Transaksi::with(['pengguna', 'pembayaran', 'details.detailProduk.produk', 'alamat', 'ekspedisi'])
            ->findOrFail($id);

        // Generate PDF (untuk sekarang return view, bisa di-upgrade dengan DomPDF nanti)
        return view('admin.customer-order.invoice-pdf', [
            'order' => $transaksi,
        ]);
    }
}

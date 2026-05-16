<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
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
        $order = Transaksi::with(['pengguna', 'pembayaran', 'pembayaran.metode', 'details.produk', 'details.warna', 'alamat', 'ekspedisi', 'voucher'])
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
        $transaksi = $pembayaran->transaksi;

        // Update pembayaran status
        $pembayaran->update([
            'status_pembayaran' => 'berhasil',
            'tanggal_pembayaran' => now(),
        ]);

        // Update transaksi status
        $transaksi->update(['status' => 'pembayaran_dikonfirmasi']);

        // Log stock movement untuk setiap item
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

                    // Update variant stock
                    $variant->update(['stok' => max(0, $variant->stok - $detail->qty)]);
                }
            }
        }

        // Insert tracking log
        if (Schema::hasTable('pesanan') && Schema::hasTable('tracking_log')) {
            $pesanan = Pesanan::where('transaksi_id', $transaksi->transaksi_id)->first();
            if ($pesanan) {
                TrackingLog::create([
                    'pesanan_id' => $pesanan->pesanan_id,
                    'status' => 'Pembayaran dikonfirmasi',
                    'catatan' => 'Pembayaran telah diverifikasi oleh admin',
                    'waktu' => now(),
                ]);
            }
        }

        return redirect()->route('admin.customer-order.show', $transaksi->kode_transaksi)
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pembayaran_menunggu,pembayaran_dikonfirmasi,pesanan_diproses,pesanan_dikirim,pesanan_selesai,pesanan_dibatalkan,refund',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => $request->get('status')]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function updateResi(Request $request, $id)
    {
        $request->validate([
            'no_resi' => 'required|string|min:5|max:50',
        ]);

        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update(['no_resi' => $request->get('no_resi')]);

        // Insert tracking log
        if (Schema::hasTable('tracking_log')) {
            TrackingLog::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Resi diupdate',
                'catatan' => 'No resi: ' . $request->get('no_resi'),
                'waktu' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Nomor resi berhasil diupdate.');
    }

    public function invoicePdf($id)
    {
        $transaksi = Transaksi::with(['pengguna', 'pembayaran', 'details.produk', 'alamat', 'ekspedisi'])
            ->findOrFail($id);

        // Generate PDF (untuk sekarang return view, bisa di-upgrade dengan DomPDF nanti)
        return view('admin.customer-order.invoice-pdf', [
            'order' => $transaksi,
        ]);
    }
}

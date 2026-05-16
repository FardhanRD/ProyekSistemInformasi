<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\MetodePembayaran;
use App\Models\Pesanan;
use App\Models\TrackingLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AkunPembayaran;

class PaymentController extends Controller
{
    public function show($kodeTransaksi)
    {
        $transaksi = Transaksi::with(['details.detailProduk.produk.images', 'pembayaran.metode', 'buyer'])
            ->where('kode_transaksi', $kodeTransaksi)
            ->firstOrFail();

        // Validasi: Pastikan transaksi milik user yang login
        if ($transaksi->buyer->pengguna_id !== Auth::user()->pengguna_id) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi: Hanya tampilkan jika status 'menunggu_pembayaran'
        if ($transaksi->status !== 'menunggu_pembayaran') {
            if (in_array($transaksi->status, ['pembayaran_dikonfirmasi', 'diproses', 'dikirim', 'selesai'])) {
                return redirect()->route('tracking.show', ['kode_transaksi' => $transaksi->kode_transaksi])
                    ->with('info', 'Pembayaran untuk transaksi ini sudah lunas.');
            }
            return redirect()->route('order.show', ['kode_transaksi' => $transaksi->kode_transaksi])
                ->with('error', 'Transaksi ini tidak lagi menunggu pembayaran.');
        }

        $pembayaran = $transaksi->pembayaran;
        if (! $pembayaran) {
            abort(404, 'Data pembayaran tidak ditemukan.');
        }

        // Cek dan update status jika kadaluarsa
        $isExpired = $pembayaran->expired_at && now()->gt($pembayaran->expired_at);
        if ($isExpired && $pembayaran->status_pembayaran === 'menunggu') {
            DB::transaction(function () use ($transaksi, $pembayaran) {
                $transaksi->update(['status' => 'dibatalkan']);
                $pembayaran->update(['status_pembayaran' => 'kadaluarsa']);
            });
        }

        // Ambil info rekening bank dari config (jika ada) atau dari tabel
        $akunPembayaran = AkunPembayaran::where('is_active', true)
            ->where('metode_id', $pembayaran->metode_id)
            ->first();

        return view('buyer.payment.pay', compact('transaksi', 'pembayaran', 'isExpired', 'akunPembayaran'));
    }

    public function uploadProof(Request $request, $kodeTransaksi)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $transaksi = Transaksi::with('pembayaran', 'buyer')
            ->where('kode_transaksi', $kodeTransaksi)
            ->firstOrFail();

        // Validasi kepemilikan
        if ($transaksi->buyer->pengguna_id !== Auth::user()->pengguna_id) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi status
        if ($transaksi->status !== 'menunggu_pembayaran') {
            return back()->with('error', 'Transaksi ini tidak lagi menunggu pembayaran.');
        }

        $pembayaran = $transaksi->pembayaran;

        // Cek kadaluarsa
        if ($pembayaran->expired_at && now()->gt($pembayaran->expired_at)) {
            return back()->with('error', 'Waktu pembayaran untuk transaksi ini sudah habis.');
        }

        DB::beginTransaction();
        try {
            // Simpan file bukti pembayaran
            $path = $request->file('bukti_pembayaran')->store('proofs', 'public');

            // Update status pembayaran dan transaksi
            $pembayaran->update([
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'menunggu_konfirmasi',
                'tanggal_pembayaran' => now(),
            ]);
            $transaksi->update(['status' => 'pembayaran_dikonfirmasi']);

            DB::commit();
            return redirect()->route('order.show', ['kode_transaksi' => $transaksi->kode_transaksi])->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu konfirmasi dari admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }
}

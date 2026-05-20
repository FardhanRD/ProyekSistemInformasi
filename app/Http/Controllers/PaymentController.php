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

        // Generate VA jika belum ada dan metode adalah transfer (cek kolom agar tidak error bila belum ditambahkan)
        try {
            if (Schema::hasColumn('pembayaran', 'nomor_va')) {
                $metodeObj = $pembayaran->metode ?? null;
                $jenis = (string) ($metodeObj->jenis ?? $pembayaran->metodePembayaran->jenis ?? '');
                if (empty($pembayaran->nomor_va) && str_contains(strtolower($jenis), 'transfer')) {
                    $buyerId = $transaksi->buyer->pengguna_id ?? null;
                    $noVA = $this->generateVA($metodeObj->metode ?? ($pembayaran->metodePembayaran->metode ?? ''), $buyerId, $transaksi->transaksi_id);
                    if ($noVA) {
                        $pembayaran->update(['nomor_va' => $noVA]);
                        // refresh model
                        $pembayaran->refresh();
                    }
                }
            }
    } catch (\Throwable $e) {
            // jangan ganggu alur bila ada masalah dengan skema db
        }

        // Ambil info rekening bank dari config (jika ada) atau dari tabel
        $akunPembayaran = AkunPembayaran::where('is_active', true)
            ->where('metode_id', $pembayaran->metode_id)
            ->first();

        // Format nomor VA dengan dash untuk display
        $nomorVAFormatted = $pembayaran->nomor_va ? $this->formatVAForDisplay($pembayaran->nomor_va) : null;

        return view('buyer.payment.pay', compact('transaksi', 'pembayaran', 'isExpired', 'akunPembayaran', 'nomorVAFormatted'));
    }

    /**
     * Generate Virtual Account number for transfer methods.
     * Format: [prefix(4)][buyerPart(6)][transPart(4)] = 14 digits total
     */
    private function generateVA($metode, $buyerId, $transaksiId)
    {
        $metode = (string) ($metode ?? '');
        $lower = strtolower($metode);

        $prefix = '9999';
        if (str_contains($lower, 'bca')) {
            $prefix = '1234';
        } elseif (str_contains($lower, 'mandiri')) {
            $prefix = '8888';
        } elseif (str_contains($lower, 'bni')) {
            $prefix = '8888';
        } elseif (str_contains($lower, 'bri')) {
            $prefix = '0088';
        }

        $buyerPart = str_pad((int) ($buyerId ?? 0), 6, '0', STR_PAD_LEFT);
        $transPart = str_pad(((int) $transaksiId) % 10000, 4, '0', STR_PAD_LEFT);

        return $prefix . $buyerPart . $transPart;
    }

    /**
     * Format VA number for display with dashes.
     * Example: 12340000030001 -> 1234-000003-0001
     */
    private function formatVAForDisplay($noVA)
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $noVA);
        if (strlen($clean) < 14) {
            return $clean; // Return as-is if not long enough
        }
        // Format: PREFIX(4) - BUYER(6) - TRANS(4)
        return substr($clean, 0, 4) . '-' . substr($clean, 4, 6) . '-' . substr($clean, 10, 4);
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

    public function confirmByBuyer(Request $request, $kodeTransaksi)
    {
        $transaksi = Transaksi::with('pembayaran', 'buyer')
            ->where('kode_transaksi', $kodeTransaksi)
            ->firstOrFail();

        if ($transaksi->buyer->pengguna_id !== Auth::user()->pengguna_id) {
            abort(403, 'Akses ditolak.');
        }

        if ($transaksi->status !== 'menunggu_pembayaran') {
            return redirect()->route('orders.index')
                ->with('info', 'Pembayaran untuk transaksi ini sudah diproses.');
        }

        $pembayaran = $transaksi->pembayaran;
        if (! $pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($pembayaran->expired_at && now()->gt($pembayaran->expired_at)) {
            return back()->with('error', 'Waktu pembayaran untuk transaksi ini sudah habis.');
        }

        DB::transaction(function () use ($transaksi, $pembayaran) {
            $pembayaran->update([
                'status_pembayaran' => 'berhasil',
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran ?: now(),
            ]);

            $transaksi->update(['status' => 'pembayaran_dikonfirmasi']);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }
}

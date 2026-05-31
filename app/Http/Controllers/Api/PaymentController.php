<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function confirmByBuyer($kodeTransaksi)
    {
        $transaksi = Transaksi::with('pembayaran', 'buyer')
            ->where('kode_transaksi', $kodeTransaksi)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $user = auth()->user();
        if ($transaksi->buyer->pengguna_id !== $user->pengguna_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if ($transaksi->status !== 'menunggu_pembayaran') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini tidak sedang menunggu pembayaran'], 422);
        }

        $pembayaran = $transaksi->pembayaran;
        if (!$pembayaran) {
            return response()->json(['success' => false, 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        if ($pembayaran->expired_at && now()->gt($pembayaran->expired_at)) {
            Transaksi::cancelExpired($transaksi->pengguna_id);
            return response()->json(['success' => false, 'message' => 'Waktu pembayaran sudah habis'], 422);
        }

        DB::transaction(function () use ($transaksi, $pembayaran) {
            $pembayaran->update([
                'status_pembayaran' => 'menunggu_konfirmasi',
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran ?: now(),
            ]);

            $transaksi->update(['status' => 'diproses']);

            if (\Illuminate\Support\Facades\Schema::hasTable('pesanan')) {
                $pesanan = \App\Models\Pesanan::where('transaksi_id', $transaksi->transaksi_id)->first();
                if ($pesanan) {
                    $pesanan->update([
                        'status_pesanan' => 'menunggu_konfirmasi',
                    ]);

                    if (\Illuminate\Support\Facades\Schema::hasTable('tracking_log')) {
                        \App\Models\TrackingLog::create([
                            'pesanan_id' => $pesanan->pesanan_id,
                            'status' => 'Menunggu Verifikasi',
                            'deskripsi' => 'Bukti pembayaran sedang ditinjau oleh admin.',
                            'lokasi' => 'Sistem',
                            'waktu_update' => now(),
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi, menunggu verifikasi admin.',
            'status' => 'diproses'
        ], 200);
    }

    public function uploadProof(Request $request, $kodeTransaksi)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $transaksi = Transaksi::with('pembayaran', 'buyer')
            ->where('kode_transaksi', $kodeTransaksi)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        $user = auth()->user();
        if ($transaksi->buyer->pengguna_id !== $user->pengguna_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if ($transaksi->status !== 'menunggu_pembayaran') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini tidak sedang menunggu pembayaran'], 422);
        }

        $pembayaran = $transaksi->pembayaran;
        if (!$pembayaran) {
            return response()->json(['success' => false, 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        if ($pembayaran->expired_at && now()->gt($pembayaran->expired_at)) {
            Transaksi::cancelExpired($transaksi->pengguna_id);
            return response()->json(['success' => false, 'message' => 'Waktu pembayaran sudah habis'], 422);
        }

        DB::beginTransaction();
        try {
            $path = $request->file('bukti_pembayaran')->store('proofs', 'public');

            $pembayaran->update([
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'menunggu_konfirmasi',
                'tanggal_pembayaran' => now(),
            ]);
            $transaksi->update(['status' => 'diproses']);

            if (\Illuminate\Support\Facades\Schema::hasTable('pesanan')) {
                $pesanan = \App\Models\Pesanan::where('transaksi_id', $transaksi->transaksi_id)->first();
                if ($pesanan) {
                    $pesanan->update([
                        'status_pesanan' => 'menunggu_konfirmasi',
                    ]);

                    if (\Illuminate\Support\Facades\Schema::hasTable('tracking_log')) {
                        \App\Models\TrackingLog::create([
                            'pesanan_id' => $pesanan->pesanan_id,
                            'status' => 'Menunggu Verifikasi',
                            'deskripsi' => 'Bukti pembayaran telah diunggah dan sedang ditinjau oleh admin.',
                            'lokasi' => 'Sistem',
                            'waktu_update' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.',
                'proof_url' => url('storage/' . $path),
                'status' => 'diproses'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}

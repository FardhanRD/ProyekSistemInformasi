<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherKlaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $buyer = $user ? $user->buyer : null;

        if (!Schema::hasTable('voucher')) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        }

        $vouchers = Voucher::where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('berlaku_sampai')
                  ->orWhere('berlaku_sampai', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('berlaku_mulai')
                  ->orWhere('berlaku_mulai', '<=', now());
            })
            ->get();

        $claimedIds = [];
        if ($buyer && Schema::hasTable('voucher_klaim')) {
            $claimedIds = VoucherKlaim::where('buyer_id', $buyer->buyer_id)
                ->where('status', 'aktif')
                ->pluck('voucher_id')
                ->toArray();
        }

        $data = $vouchers->map(function ($v) use ($claimedIds) {
            return [
                'id' => $v->voucher_id,
                'kode_voucher' => $v->kode_voucher,
                'nama_voucher' => $v->nama_voucher,
                'deskripsi' => $v->deskripsi,
                'jenis_diskon' => $v->jenis_diskon,
                'nilai_diskon' => (int)$v->nilai_diskon,
                'min_belanja' => (int)$v->min_belanja,
                'maks_diskon' => (int)$v->maks_diskon,
                'berlaku_mulai' => $v->berlaku_mulai,
                'berlaku_sampai' => $v->berlaku_sampai,
                'is_claimed' => in_array($v->voucher_id, $claimedIds),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function claimed(Request $request)
    {
        $user = auth()->user();
        $buyer = $user ? $user->buyer : null;

        if (!$buyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data buyer tidak ditemukan'
            ], 403);
        }

        if (!Schema::hasTable('voucher_klaim') || !Schema::hasTable('voucher')) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        }

        $claimed = VoucherKlaim::with('voucher')
            ->where('buyer_id', $buyer->buyer_id)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($claim) {
                $v = $claim->voucher;
                if (!$v) return null;
                return [
                    'id' => $v->voucher_id,
                    'kode_voucher' => $v->kode_voucher,
                    'nama_voucher' => $v->nama_voucher,
                    'deskripsi' => $v->deskripsi,
                    'jenis_diskon' => $v->jenis_diskon,
                    'nilai_diskon' => (int)$v->nilai_diskon,
                    'min_belanja' => (int)$v->min_belanja,
                    'maks_diskon' => (int)$v->maks_diskon,
                    'berlaku_mulai' => $v->berlaku_mulai,
                    'berlaku_sampai' => $v->berlaku_sampai,
                    'is_claimed' => true,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $claimed
        ], 200);
    }

    public function claim(Request $request)
    {
        $user = auth()->user();
        $buyer = $user ? $user->buyer : null;

        if (!$buyer) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Data buyer tidak ditemukan'
            ], 403);
        }

        $kode = strtoupper(trim((string)$request->input('kode_voucher')));

        if (!$kode) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Kode voucher harus diisi'
            ], 400);
        }

        if (!Schema::hasTable('voucher')) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Tabel voucher tidak tersedia'
            ], 500);
        }

        $voucher = Voucher::where('kode_voucher', $kode)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('berlaku_sampai')
                  ->orWhere('berlaku_sampai', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('berlaku_mulai')
                  ->orWhere('berlaku_mulai', '<=', now());
            })
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => '❌ Kode voucher tidak ditemukan atau sudah tidak berlaku'
            ], 404);
        }

        if (!Schema::hasTable('voucher_klaim')) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Tabel voucher_klaim belum tersedia.'
            ], 500);
        }

        $sudahKlaim = VoucherKlaim::where('voucher_id', $voucher->voucher_id)
            ->where('buyer_id', $buyer->buyer_id)
            ->exists();

        if ($sudahKlaim) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Anda sudah pernah mengklaim voucher ini'
            ], 400);
        }

        if ($voucher->kuota !== null) {
            $terpakai = VoucherKlaim::where('voucher_id', $voucher->voucher_id)->count();
            if ($terpakai >= $voucher->kuota) {
                return response()->json([
                    'success' => false,
                    'message' => '😔 Kuota voucher sudah habis'
                ], 400);
            }
        }

        VoucherKlaim::create([
            'voucher_id' => $voucher->voucher_id,
            'buyer_id' => $buyer->buyer_id,
            'status' => 'aktif',
            'diklaim_at' => now(),
        ]);

        $diskonText = $voucher->jenis_diskon === 'persen'
            ? 'Diskon ' . $voucher->nilai_diskon . '%'
            : 'Diskon Rp ' . number_format($voucher->nilai_diskon, 0, ',', '.');

        return response()->json([
            'success' => true,
            'message' => '✅ Voucher berhasil diklaim! ' . $diskonText . ' siap digunakan'
        ], 200);
    }
}

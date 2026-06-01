<?php

namespace App\Http\Controllers;

use App\Models\VoucherKlaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Schema::hasTable('voucher') ? Voucher::where('is_active',1)->get() : collect();
        return view('voucher.index', compact('vouchers'));
    }

    public function apply(Request $request)
    {
        $code = $request->input('kode_voucher', $request->input('kode'));
        if (! Schema::hasTable('voucher')) return back()->with('error','Voucher tidak tersedia');
        $v = Voucher::where('kode_voucher', $code)->where('is_active',1)->first();
        if (! $v) return back()->with('error','Kode voucher tidak valid');
        session([
            'applied_voucher_code' => $v->kode_voucher,
            'applied_voucher_id' => $v->voucher_id,
        ]);
        return back()->with('success','Voucher diterapkan');
    }

    public function klaim(Request $request)
    {
        $buyer = auth()->user()?->buyer;

        if (! $buyer) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Data buyer tidak ditemukan',
            ], 403);
        }

        $kode = strtoupper(trim((string) $request->input('kode_voucher')));

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

        if (! $voucher) {
            return response()->json([
                'success' => false,
                'message' => '❌ Kode voucher tidak ditemukan atau sudah tidak berlaku',
            ]);
        }

        if (! Schema::hasTable('voucher_klaim')) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Tabel voucher_klaim belum tersedia. Jalankan migration terlebih dahulu.',
            ], 500);
        }

        $sudahKlaim = VoucherKlaim::where('voucher_id', $voucher->voucher_id)
            ->where('buyer_id', $buyer->buyer_id)
            ->exists();

        if ($sudahKlaim) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Kamu sudah pernah mengklaim voucher ini',
            ]);
        }

        if ($voucher->kuota !== null) {
            $terpakai = VoucherKlaim::where('voucher_id', $voucher->voucher_id)->count();

            if ($terpakai >= $voucher->kuota) {
                return response()->json([
                    'success' => false,
                    'message' => '😔 Kuota voucher sudah habis',
                ]);
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
            'message' => '✅ Voucher berhasil diklaim! ' . $diskonText . ' siap digunakan',
        ]);
    }
}

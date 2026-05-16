<?php

namespace App\Http\Controllers;

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
}

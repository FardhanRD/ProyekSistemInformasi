<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Supplier;
use App\Models\RatingToko;
use App\Services\PenggunaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RatingTokoController extends Controller
{
    public function form($supplierId)
    {
        if (! Schema::hasTable('supplier') || ! Schema::hasTable('rating_toko')) abort(404);
        if (! $this->canRateStore(Auth::user(), $supplierId)) {
            abort(403, 'Rating toko hanya bisa diberikan setelah transaksi selesai.');
        }
        $supplier = Supplier::findOrFail($supplierId);
        return view('rating.toko', compact('supplier'));
    }

    public function submit(Request $request, $supplierId, PenggunaSyncService $penggunaSyncService)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');
        if (! Schema::hasTable('rating_toko')) return back()->with('error','Rating toko tidak tersedia');
        if (! $this->canRateStore($user, $supplierId)) {
            return back()->with('error', 'Rating toko hanya bisa diberikan setelah transaksi selesai.');
        }

        $data = $request->validate(['bintang'=>'required|integer|min:1|max:5','komentar'=>'nullable|string']);
        $penggunaId = $penggunaSyncService->ensureForAuthUser($user, 'buyer');
        $buyer = Buyer::firstOrCreate(['pengguna_id' => $penggunaId]);
        RatingToko::create(array_merge($data,['supplier_id'=>$supplierId,'buyer_id'=>$buyer->buyer_id]));
        return redirect()->back()->with('success','Terima kasih atas rating toko');
    }

    private function canRateStore($user, int $supplierId): bool
    {
        if (! $user) {
            return false;
        }

        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();
        if (! $buyer) {
            return false;
        }

        return \App\Models\Transaksi::where('pengguna_id', $user->pengguna_id)
            ->where('status', 'selesai')
            ->whereHas('details.detailProduk.produk', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->exists();
    }
}

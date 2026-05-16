<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\RatingProduk;
use App\Models\Produk;
use App\Services\PenggunaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RatingController extends Controller
{
    public function form($produkId)
    {
        if (! Schema::hasTable('produk') || ! Schema::hasTable('rating_produk')) abort(404);
        if (! $this->canRateProduct(Auth::user(), $produkId)) {
            abort(403, 'Rating produk hanya bisa diberikan setelah transaksi selesai.');
        }
        $product = Produk::findOrFail($produkId);
        return view('rating.product', compact('product'));
    }

    public function submit(Request $request, $produkId, PenggunaSyncService $penggunaSyncService)
    {
        $user = Auth::user();
        if (! $user) return redirect('/login');
        if (! Schema::hasTable('rating_produk')) return back()->with('error','Rating tidak tersedia');
        if (! $this->canRateProduct($user, $produkId)) {
            return back()->with('error', 'Rating produk hanya bisa diberikan setelah transaksi selesai.');
        }

        $data = $request->validate(['bintang'=>'required|integer|min:1|max:5','judul_ulasan'=>'nullable|string|max:200','isi_ulasan'=>'nullable|string']);
        $penggunaId = $penggunaSyncService->ensureForAuthUser($user, 'buyer');
        $buyer = Buyer::firstOrCreate(['pengguna_id' => $penggunaId]);
        RatingProduk::create(array_merge($data,['produk_id'=>$produkId,'buyer_id'=>$buyer->buyer_id,'transaksi_id'=>null]));
        return redirect()->route('product.show', ['slug'=> Produk::find($produkId)->slug])->with('success','Terima kasih atas ulasan Anda');
    }

    private function canRateProduct($user, int $produkId): bool
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
            ->whereHas('details.detailProduk', function ($query) use ($produkId) {
                $query->where('produk_id', $produkId);
            })
            ->exists();
    }
}

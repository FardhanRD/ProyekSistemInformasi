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
        $bintang = (int) $data['bintang'];
        $attributes = [
            'supplier_id' => $supplierId,
            'buyer_id' => $buyer->buyer_id,
        ];

        if (Schema::hasColumn('rating_toko', 'kategori')) {
            $attributes['kategori'] = 'pelayanan';
        }

        RatingToko::updateOrCreate(
            $attributes,
            [
                'pelayanan' => Schema::hasColumn('rating_toko', 'pelayanan') ? $bintang : null,
                'aplikasi' => Schema::hasColumn('rating_toko', 'aplikasi') ? $bintang : null,
                'bintang' => $bintang,
                'komentar' => $data['komentar'] ?? null,
            ]
        );
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

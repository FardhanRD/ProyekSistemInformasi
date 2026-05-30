<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\RatingProduk;
use App\Models\RatingToko;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\PenggunaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function show($kodeTransaksi)
    {
        $user = auth()->user();
        
        $transaksi = Transaksi::where('kode_transaksi', $kodeTransaksi)
            ->where('pengguna_id', $user->pengguna_id)
            ->where('status', 'selesai')
            ->with('details.detailProduk.produk.gambarUtama')
            ->firstOrFail();
        
        // Get supplier from first product
        $firstDetail = $transaksi->details->first();
        $supplier = $firstDetail->detailProduk->produk->supplier;
        
        // Prepare produkRatings for Alpine.js
        $produkRatings = $transaksi->details->map(fn($d) => [
            'detail_id'   => $d->detail_id,
            'produk_id'   => $d->detailProduk->produk_id,
            'nama'        => $d->nama_produk_snap,
            'gambar'      => $d->detailProduk->produk->gambarUtama?->url_safe ?? '',
            'bintang'     => 0,
            'judul'       => '',
            'isi'         => '',
        ])->values()->all();
        
        return view('buyer.order.rating', [
            'transaksi' => $transaksi,
            'supplier'  => $supplier,
            'produkRatings' => $produkRatings,
        ]);
    }

    public function store(Request $request, $kodeTransaksi)
    {
        $user = auth()->user();
        $buyer = $user?->buyer;

        if (! $user || ! $buyer) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login kembali untuk memberi rating.',
            ], 401);
        }

        $transaksi = Transaksi::with(['transaksiDetail.detailProduk.produk'])
            ->where('kode_transaksi', $kodeTransaksi)
            ->where('pengguna_id', $user->pengguna_id)
            ->where('status', 'selesai')
            ->firstOrFail();

        $validated = $request->validate([
            'produk_ratings' => 'nullable|array',
            'produk_ratings.*.bintang' => 'nullable|integer|min:0|max:5',
            'toko_rating' => 'nullable|array',
            'toko_rating.pelayanan' => 'nullable|integer|min:1|max:5',
            'toko_rating.aplikasi' => 'nullable|integer|min:1|max:5',
        ]);

        DB::beginTransaction();
        try {
            foreach (($validated['produk_ratings'] ?? []) as $rating) {
                $bintang = (int) ($rating['bintang'] ?? 0);

                if ($bintang > 0) {
                    $produkId = $rating['produk_id'] ?? null;

                    if (! $produkId) {
                        continue;
                    }

                    RatingProduk::updateOrCreate(
                        [
                            'produk_id'    => $produkId,
                            'buyer_id'     => $buyer->buyer_id,
                            'transaksi_id' => $transaksi->transaksi_id,
                        ],
                        [
                            'bintang'      => $bintang,
                            'judul_ulasan' => $rating['judul'] ?? null,
                            'isi_ulasan'   => $rating['isi'] ?? null,
                            'is_verified'  => 1,
                        ]
                    );
                }
            }

            $tokoRating = $validated['toko_rating'] ?? [];
            $supplierId = $transaksi->transaksiDetail->first()?->detailProduk?->produk?->supplier_id;
            $pelayanan = (int) ($tokoRating['pelayanan'] ?? 0);
            $aplikasi = (int) ($tokoRating['aplikasi'] ?? 0);

            if ($supplierId && Schema::hasColumn('rating_toko', 'kategori')) {
                if ($pelayanan > 0) {
                    RatingToko::updateOrCreate(
                        [
                            'supplier_id' => $supplierId,
                            'buyer_id'    => $buyer->buyer_id,
                            'kategori'    => 'pelayanan',
                        ],
                        [
                            'bintang'  => $pelayanan,
                            'komentar' => $tokoRating['komentar'] ?? null,
                        ]
                    );
                }

                if ($aplikasi > 0) {
                    RatingToko::updateOrCreate(
                        [
                            'supplier_id' => $supplierId,
                            'buyer_id'    => $buyer->buyer_id,
                            'kategori'    => 'aplikasi',
                        ],
                        [
                            'bintang'  => $aplikasi,
                            'komentar' => $tokoRating['komentar'] ?? null,
                        ]
                    );
                }
            } elseif ($supplierId && ($pelayanan > 0 || $aplikasi > 0)) {
                $scores = array_values(array_filter([$pelayanan, $aplikasi], fn ($value) => $value > 0));
                $bintangToko = $scores ? (int) round(array_sum($scores) / count($scores)) : 0;

                if ($bintangToko > 0) {
                    RatingToko::updateOrCreate(
                        [
                            'supplier_id' => $supplierId,
                            'buyer_id'    => $buyer->buyer_id,
                        ],
                        [
                            'bintang'  => $bintangToko,
                            'komentar' => $tokoRating['komentar'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

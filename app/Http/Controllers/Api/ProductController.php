<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::where('is_active', 1)->with(['images', 'kategori.parent', 'details']);

        if ($request->has('type')) {
            $type = $request->input('type');
            if ($type === 'new_arrivals') {
                $orderField = \Illuminate\Support\Facades\Schema::hasColumn('produk', 'penyimpanan_waktu')
                    ? 'penyimpanan_waktu'
                    : (\Illuminate\Support\Facades\Schema::hasColumn('produk', 'created_at') ? 'created_at' : 'produk_id');
                $query->where('status_publish', 'publish')
                      ->orderByDesc($orderField)
                      ->limit(8);
            } elseif ($type === 'best_sellers') {
                $query->orderByDesc('total_terjual')
                      ->limit(8);
            } elseif ($type === 'recommended') {
                $query->inRandomOrder()
                      ->limit(8);
            } elseif ($type === 'flash_sale') {
                $now = \Carbon\Carbon::now();
                if (\Illuminate\Support\Facades\Schema::hasTable('promo')) {
                    $flashProductIds = \DB::table('promo')
                        ->where('jenis', 'flash_sale')
                        ->where('is_active', 1)
                        ->where('mulai', '<=', $now)
                        ->where('selesai', '>=', $now)
                        ->pluck('produk_id')
                        ->unique()
                        ->toArray();
                    $query->whereIn('produk_id', $flashProductIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        $products = $query->get();
        $formatted = $this->formatProducts($products);

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ], 200);
    }

    private function formatProducts($products)
    {
        $productIds = $products->pluck('produk_id')->toArray();
        $now = \Carbon\Carbon::now();
        $activePromos = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('promo') && !empty($productIds)) {
            $activePromos = \DB::table('promo')
                ->where('is_active', 1)
                ->whereIn('jenis', ['flash_sale', 'diskon_produk'])
                ->whereIn('produk_id', $productIds)
                ->where('mulai', '<=', $now)
                ->where('selesai', '>=', $now)
                ->get()
                ->keyBy('produk_id');
        }

        return $products->map(function ($p) use ($activePromos) {
            $promo = $activePromos[$p->produk_id] ?? null;
            $originalPrice = (float) $p->harga_dasar;
            $price = $originalPrice;
            $discountPercent = 0;
            $badge = null;

            if ($promo) {
                $nominal = (float) ($promo->diskon ?? $promo->nominal_diskon ?? 0);
                if ($nominal <= 0 && !empty($promo->persen_diskon)) {
                    $nominal = $originalPrice * ((float) $promo->persen_diskon) / 100;
                    $discountPercent = (float) $promo->persen_diskon;
                } else if ($nominal > 0) {
                    $discountPercent = ($originalPrice > 0) ? round(($nominal / $originalPrice) * 100) : 0;
                }
                $price = max(0.0, $originalPrice - $nominal);
                $badge = ($promo->jenis === 'flash_sale') ? 'FLASH SALE' : 'SALE';
            }

            return [
                'id' => $p->produk_id,
                'name' => $p->nama_produk,
                'original_price' => $originalPrice,
                'price' => $price,
                'discount_percent' => $discountPercent,
                'badge' => $badge,
                'sale_end_time' => $promo ? $promo->selesai : null,
                'description' => $p->deskripsi ?? '',
                'image' => $p->images->first()->url_gambar ?? '',
                'gallery' => $p->images->pluck('url_gambar')->toArray(),
                'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum"),
                'details' => $p->details->map(function ($d) {
                    return [
                        'detail_id' => $d->detail_produk_id,
                        'size' => $d->ukuran,
                        'stock' => $d->stok,
                        'price' => $d->harga,
                    ];
                })
            ];
        });
    }

    public function getByCategory($id) {
        $products = \App\Models\Produk::where('kategori_id', $id)->with(['images', 'kategori.parent', 'details'])->get();
        $formatted = $this->formatProducts($products);
        return response()->json(['status'=>'success', 'data'=>$formatted], 200);
    }

    public function show($id)
    {
        $p = Produk::with(['images', 'kategori.parent', 'details'])->find($id);
        if (!$p) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
        }
        
        $formatted = $this->formatProducts(collect([$p]))->first();

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ], 200);
    }
}
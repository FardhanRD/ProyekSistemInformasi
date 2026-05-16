<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Produk;
use App\Models\Kategori;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Banners
        // Banners (safe for test env: table may not exist)
        $banners = [];
        if (Schema::hasTable('banner')) {
            $banners = DB::table('banner')->where('is_active', 1)->orderBy('urutan')->get();
            // Ensure banner URLs are properly formatted
            $banners = $banners->map(function($banner) {
                if (isset($banner->url_gambar) && !str_starts_with($banner->url_gambar, 'http')) {
                    $banner->url_gambar = asset('storage/' . $banner->url_gambar);
                }
                return $banner;
            });
        }

        if (empty($banners) || ($banners instanceof \Illuminate\Support\Collection && $banners->isEmpty()) ) {

            $banners = collect([ (object) [
                'url_gambar' => asset('images/default-banner.svg'),
                'judul' => 'Selamat datang di MOVR',
                'sub_judul' => 'Temukan produk favoritmu di sini',
                'url_link' => url('/produk')
            ]]);
        }

        // New Arrivals
        $newArrivals = collect();
        $bestSellers = collect();

        if (Schema::hasTable('produk')) {
            $orderField = Schema::hasColumn('produk', 'penyimpanan_waktu')
                ? 'penyimpanan_waktu'
                : (Schema::hasColumn('produk', 'created_at') ? 'created_at' : 'produk_id');

            $newArrivals = Produk::where('is_active', 1)
                ->where('status_publish', 'publish')
                ->with('images')
                ->orderByDesc($orderField)
                ->limit(8)
                ->get();

            $bestSellers = Produk::where('is_active', 1)
                ->with('images')
                ->orderByDesc('total_terjual')
                ->limit(8)
                ->get();
        }


        // Flash Sale (promos)
        $flashProducts = collect();
        if (Schema::hasTable('promo')) {
            $now = Carbon::now();
            $flashPromos = DB::table('promo')
                ->where('jenis', 'flash_sale')
                ->where('is_active', 1)
                ->where('mulai', '<=', $now)
                ->where('selesai', '>=', $now)
                ->get();

            $produk_ids = $flashPromos->pluck('produk_id')->unique()->toArray();
            $products = Produk::whereIn('produk_id', $produk_ids)->with('images')->get()->keyBy('produk_id');

            foreach ($flashPromos as $promo) {
                if (isset($promo->produk_id) && isset($products[$promo->produk_id])) {
                    $p = $products[$promo->produk_id];
                    $nominal = (float) ($promo->nominal_diskon ?? 0);
                    if ($nominal <= 0 && !empty($promo->persen_diskon)) {
                        $nominal = ((float) $p->harga_dasar) * ((float) $promo->persen_diskon) / 100;
                    }
                    $promo->diskon = $nominal;
                    $flashProducts->push((object) ['produk' => $p, 'promo' => $promo]);
                }
            }

            $flashProducts = $flashProducts->unique('produk.produk_id')->take(8);
        }


        // Quick categories (MAN, WOMEN, KIDS)
        $quickCategories = collect();
        if (Schema::hasTable('kategori')) {
            $quickCategories = Kategori::where('level', 1)
                ->where('is_active', 1)
                ->whereIn('nama_kategori', ['MAN', 'WOMEN', 'KIDS'])
                ->orderBy('urutan')
                ->get();
        }


        return view('buyer.home', compact('banners','newArrivals','bestSellers','flashProducts','quickCategories'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // --- Data untuk Filter ---
        $filterData = [
            'categories' => Kategori::whereNull('parent_id')->with('children.children')->orderBy('nama_kategori')->get(),
            'sizes' => DetailProduk::whereNotNull('ukuran')->where('ukuran', '!=', '')->distinct()->orderBy('ukuran')->pluck('ukuran'),
            'colors' => collect(), // warna column tidak ada di detail_produk
            'maxPrice' => Produk::where('is_active', 1)->max('harga_dasar'),
        ];

        // --- Query Utama ---
        $query = Produk::with(['images', 'details', 'kategori'])
            ->withAvg('ratings as average_rating', 'bintang')
            ->where('is_active',1);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nama_produk', 'like', "%{$q}%");
        }
        if ($request->filled('kategori')) {
            $kategoriIds = is_array($request->kategori) ? $request->kategori : [$request->kategori];
            $query->whereIn('kategori_id', $kategoriIds);
        }
        if ($request->filled('min_price')) {
            $query->where('harga_dasar', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('harga_dasar', '<=', $request->max_price);
        }
        if ($request->filled('sizes')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereIn('ukuran', $request->sizes);
            });
        }
        // if ($request->filled('colors')) {
        //     $query->whereHas('details', function ($q) use ($request) {
        //         $q->whereIn('warna', $request->colors);
        //     });
        // }

        if ($request->filled('rating')) {
            $query->havingRaw('COALESCE(average_rating, 0) >= ?', [(float) $request->rating]);
        }

        if ($request->filled('gender')) {
            $gender = strtolower((string) $request->gender);
            $genderSlugs = match ($gender) {
                'cowo' => ['man'],
                'cewe' => ['women'],
                'unisex' => ['man', 'women', 'kids'],
                default => [],
            };

            if (! empty($genderSlugs)) {
                $genderCategoryIds = \App\Models\Kategori::whereIn('slug', $genderSlugs)->pluck('kategori_id')->toArray();
                $allIds = [];
                foreach ($genderCategoryIds as $categoryId) {
                    $category = \App\Models\Kategori::with('children.children')->find($categoryId);
                    if ($category) {
                        $allIds = array_merge($allIds, $category->descendantIds());
                    }
                }
                $query->whereIn('kategori_id', array_values(array_unique($allIds)));
            }
        }

        // --- Sorting ---
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlaris':
                $query->orderByDesc('total_terjual');
                break;
            case 'harga_terendah':
                $query->orderBy('harga_dasar', 'asc');
                break;
            case 'harga_tertinggi':
                $query->orderBy('harga_dasar', 'desc');
                break;
            case 'rating_tertinggi':
                $query->orderByDesc('average_rating');
                break;
            default: // terbaru
                $query->orderByDesc('penyimpanan_waktu');
                break;
        }

        $products = $query->paginate(20)->withQueryString();
        return view('buyer.category.index', compact('products', 'filterData'));
    }

    public function show($slug)
    {
        $product = Produk::with([
            'images' => fn($q) => $q->orderBy('urutan', 'ASC'),
            'details' => fn($q) => $q->where('is_active', 1),
            'kategori',
            'ratings' => fn($q) => $q->latest()->limit(100),
            'ratings.buyer.pengguna'
        ])
            ->withAvg('ratings as average_rating', 'bintang')
            ->withCount('ratings as review_count')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        // Ambil produk serupa dari kategori yang sama (exclude produk saat ini)
        $similarProducts = Produk::where('kategori_id', $product->kategori_id)
            ->where('produk_id', '!=', $product->produk_id)
            ->where('is_active', 1)
            ->where('status_publish', 'publish')
            ->with(['images' => fn($q) => $q->orderBy('urutan', 'ASC')])
            ->withAvg('ratings as average_rating', 'bintang')
            ->limit(4)
            ->get();

        $promoAktif = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('promo')) {
            $promoAktif = DB::table('promo')
                ->where('is_active', 1)
                ->whereIn('jenis', ['flash_sale', 'diskon_produk'])
                ->where('produk_id', $product->produk_id)
                ->where('mulai', '<=', Carbon::now())
                ->where('selesai', '>=', Carbon::now())
                ->first();
        }

        // Hitung statistik rating
        $ratingStats = [
            'total' => $product->ratings->count(),
            'average' => round($product->average_rating ?? 0, 1),
            'distribution' => [
                5 => $product->ratings->where('bintang', 5)->count(),
                4 => $product->ratings->where('bintang', 4)->count(),
                3 => $product->ratings->where('bintang', 3)->count(),
                2 => $product->ratings->where('bintang', 2)->count(),
                1 => $product->ratings->where('bintang', 1)->count(),
            ]
        ];

        // Cek apakah user sudah membeli produk ini
        $hasPurchased = false;
        $hasReviewed = false;
        if (auth()->check()) {
            $penggunaId = auth()->user()->pengguna_id;
            $hasPurchased = DB::table('transaksi_detail')
                ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
                ->join('detail_produk', 'transaksi_detail.detail_produk_id', '=', 'detail_produk.detail_produk_id')
                ->where('transaksi.pengguna_id', $penggunaId)
                ->where('detail_produk.produk_id', $product->produk_id)
                ->where('transaksi.status', 'selesai')
                ->exists();

            $hasReviewed = $product->ratings
                ->where('buyer.pengguna_id', $penggunaId)
                ->isNotEmpty();
        }

        return view('buyer.product.detail', compact(
            'product',
            'similarProducts',
            'promoAktif',
            'ratingStats',
            'hasPurchased',
            'hasReviewed'
        ));
    }

    public function search(Request $request)
    {
        // Ambil query pencarian dari parameter 'q'
        $q = $request->input('q', '');

        // --- Data untuk Filter ---
        $filterData = [
            'categories' => Kategori::whereNull('parent_id')->with('children.children')->orderBy('nama_kategori')->get(),
            'sizes' => DetailProduk::whereNotNull('ukuran')->where('ukuran', '!=', '')->distinct()->orderBy('ukuran')->pluck('ukuran'),
            'colors' => collect(), // warna column tidak ada di detail_produk
            'maxPrice' => Produk::where('is_active', 1)->max('harga_dasar'),
        ];

        // --- Query Utama dengan Search ---
        $query = Produk::with(['images', 'details', 'kategori'])
            ->withAvg('ratings as average_rating', 'bintang')
            ->where('is_active', 1)
            ->where('status_publish', 'publish');

        // Search di nama_produk atau deskripsi
        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_produk', 'like', "%{$q}%")
                  ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        // Terapkan filter tambahan
        if ($request->filled('kategori')) {
            $kategoriIds = is_array($request->kategori) ? $request->kategori : [$request->kategori];
            $query->whereIn('kategori_id', $kategoriIds);
        }
        if ($request->filled('min_price')) {
            $query->where('harga_dasar', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('harga_dasar', '<=', $request->max_price);
        }
        if ($request->filled('sizes')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereIn('ukuran', $request->sizes);
            });
        }
        // if ($request->filled('colors')) {
        //     $query->whereHas('details', function ($q) use ($request) {
        //         $q->whereIn('warna', $request->colors);
        //     });
        // }
        if ($request->filled('rating')) {
            $query->havingRaw('COALESCE(average_rating, 0) >= ?', [(float) $request->rating]);
        }
        if ($request->filled('gender')) {
            $gender = strtolower((string) $request->gender);
            $genderSlugs = match ($gender) {
                'cowo' => ['man'],
                'cewe' => ['women'],
                'kids' => ['kids'],
                'unisex' => ['man', 'women', 'kids'],
                default => [],
            };

            if (!empty($genderSlugs)) {
                $genderCategoryIds = Kategori::whereIn('slug', $genderSlugs)->pluck('kategori_id')->toArray();
                $allIds = [];
                foreach ($genderCategoryIds as $categoryId) {
                    $category = Kategori::with('children.children')->find($categoryId);
                    if ($category) {
                        $allIds = array_merge($allIds, $category->descendantIds());
                    }
                }
                $query->whereIn('kategori_id', array_values(array_unique($allIds)));
            }
        }

        // --- Sorting ---
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlaris':
                $query->orderByDesc('total_terjual');
                break;
            case 'harga_terendah':
                $query->orderBy('harga_dasar', 'asc');
                break;
            case 'harga_tertinggi':
                $query->orderBy('harga_dasar', 'desc');
                break;
            case 'rating_tertinggi':
                $query->orderByDesc('average_rating');
                break;
            default: // terbaru
                $query->orderByDesc('penyimpanan_waktu');
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        return view('buyer.category.index', compact('products', 'filterData', 'q'));
    }
}

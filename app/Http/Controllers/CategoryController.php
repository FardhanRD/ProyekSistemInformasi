<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\DetailProduk;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, $slug)
    {
        $cat = Kategori::with('children.children.children')->where('slug', $slug)->firstOrFail();
        $catIds = $cat->descendantIds();

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
            ->where('is_active', 1)
            ->whereIn('kategori_id', $catIds); // Filter utama berdasarkan kategori dari URL

        // --- Terapkan Filter Tambahan dari Request ---
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nama_produk', 'like', "%{$q}%");
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
            // Logika gender dari ProductController bisa disalin ke sini jika diperlukan
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

        // Menggunakan view yang sama dengan product.index untuk konsistensi
        return view('buyer.category.index', compact('cat', 'products', 'filterData'));
    }
}

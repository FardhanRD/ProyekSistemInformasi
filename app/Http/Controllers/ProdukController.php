<?php

namespace App\Http\Controllers;

use App\Models\Product; // Gunakan Model Product (Baru)
use App\Models\Category;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar semua produk
     */
    public function index(Request $request)
    {
        // Mulai Query dari tabel products
        $query = Product::with('category');

        // Logika Filter Kategori (Jika ada request kategori)
        if ($request->has('kategori') && $request->kategori != '') {
            $slug = $request->kategori;
            $query->whereHas('category', function($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        // Fitur Search: cari berdasarkan nama atau deskripsi
        if ($request->has('search') && trim($request->search) != '') {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        // Sort options (opsional)
        if ($request->has('sort') && $request->sort != '') {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        }

        // Ambil data dengan pagination (9 produk per halaman)
        $products = $query->paginate(9)->withQueryString();

        // Ambil semua kategori untuk sidebar filter
        $categories = Category::all();

        // Kirim juga nilai pencarian agar form menampilkan kembali kata pencarian
        $search = $request->search ?? null;

        return view('movr.produk.index', compact('products', 'categories', 'search'));
    }

    /**
     * Menampilkan detail 1 produk
     */
    /**
     * Menampilkan detail 1 produk
     */
    public function show($id)
    {
        // 1. Cari produk utama (Eager load category & penjual agar hemat query)
        $product = Product::with(['category', 'penjual', 'images'])->findOrFail($id);

        // 2. Ambil Ulasan (Pagination 5 per halaman)
        $ulasan = $product->ulasan()->with('pembeli')->latest()->paginate(5);

        // 3. TAMBAHKAN INI: Logika Produk Serupa
        // Syarat: Kategori sama, TAPI bukan produk yang sedang dibuka
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id) // Exclude produk ini sendiri
            ->inRandomOrder()        // Acak urutannya
            ->limit(3)               // Ambil 3 saja (sesuai layout grid)
            ->get();

        // 4. Kirim 'product', 'ulasan', DAN 'relatedProducts' ke view
        return view('movr.produk.show', compact('product', 'ulasan', 'relatedProducts'));
    }
}
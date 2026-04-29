<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FavoritController extends Controller
{
    /**
     * Ambil semua favorit pengguna saat ini
     */
    public function index()
    {
        $favorites = Favorit::where('user_id', auth()->id())
            ->with(['product:id,name,price,image,stock,category_id', 'product.category:id,name'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar favorit berhasil diambil',
            'total' => $favorites->count(),
            'data' => $favorites
        ]);
    }

    /**
     * Cek apakah produk ada di favorit pengguna
     */
    public function check($product_id)
    {
        $isFavorited = Favorit::where('user_id', auth()->id())
            ->where('product_id', $product_id)
            ->exists();

        return response()->json([
            'is_favorited' => $isFavorited
        ]);
    }

    /**
     * Tambah produk ke favorit
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product_id = $request->product_id;

        // Cegah favorit duplikat
        $existingFavorite = Favorit::where('user_id', auth()->id())
            ->where('product_id', $product_id)
            ->first();

        if ($existingFavorite) {
            throw ValidationException::withMessages([
                'product_id' => 'Produk sudah ada di daftar favorit Anda'
            ]);
        }

        $favorite = Favorit::create([
            'user_id' => auth()->id(),
            'product_id' => $product_id,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke favorit',
            'data' => $favorite->load('product')
        ], 201);
    }

    /**
     * Hapus favorit berdasarkan ID
     */
    public function destroy($id)
    {
        $favorite = Favorit::findOrFail($id);

        // Verifikasi kepemilikan favorit
        if ($favorite->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $favorite->delete();

        return response()->json(['message' => 'Produk berhasil dihapus dari favorit']);
    }

    /**
     * Hapus favorit berdasarkan ID produk
     */
    public function destroyByProduct($product_id)
    {
        $favorite = Favorit::where('user_id', auth()->id())
            ->where('product_id', $product_id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Produk tidak ditemukan di favorit'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Produk berhasil dihapus dari favorit']);
    }

    /**
     * Hapus semua favorit pengguna saat ini
     */
    public function clear()
    {
        Favorit::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Semua favorit berhasil dihapus']);
    }
}

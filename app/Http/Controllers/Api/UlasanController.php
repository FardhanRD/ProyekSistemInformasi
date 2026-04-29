<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UlasanController extends Controller
{
    /**
     * Ambil semua ulasan untuk produk tertentu
     */
    public function index($product_id)
    {
        $product = Product::findOrFail($product_id);
        
        $reviews = Ulasan::where('product_id', $product_id)
            ->with(['pembeli:id,name,email'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Ulasan produk berhasil diambil',
            'product' => $product->only(['id', 'name']),
            'data' => $reviews
        ]);
    }

    /**
     * Ambil detail ulasan
     */
    public function show($id)
    {
        $review = Ulasan::with(['product', 'pembeli'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail ulasan berhasil diambil',
            'data' => $review
        ]);
    }

    /**
     * Buat ulasan baru untuk produk
     */
    public function store(Request $request, $product_id)
    {
        Product::findOrFail($product_id);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:5|max:1000',
        ]);

        // Cegah ulasan duplikat dari pengguna yang sama
        $existingReview = Ulasan::where('product_id', $product_id)
            ->where('pembeli_id', auth()->id())
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'review' => 'Anda sudah memberikan ulasan untuk produk ini'
            ]);
        }

        $review = Ulasan::create([
            'product_id' => $product_id,
            'pembeli_id' => auth()->id(),
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'message' => 'Ulasan berhasil dibuat',
            'data' => $review->load(['product', 'pembeli'])
        ], 201);
    }

    /**
     * Update ulasan pengguna
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:5|max:1000',
        ]);

        $review = Ulasan::findOrFail($id);

        // Check if review belongs to current user
        if ($review->pembeli_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->update([
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'message' => 'Ulasan berhasil diupdate',
            'data' => $review->fresh(['product', 'pembeli'])
        ]);
    }

    /**
     * Hapus ulasan pengguna
     */
    public function destroy($id)
    {
        $review = Ulasan::findOrFail($id);

        // Verifikasi kepemilikan ulasan
        if ($review->pembeli_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Ulasan berhasil dihapus']);
    }

    /**
     * Ambil semua ulasan dari pengguna saat ini
     */
    public function myReviews()
    {
        $reviews = Ulasan::where('pembeli_id', auth()->id())
            ->with(['product:id,name,price'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Ulasan Anda berhasil diambil',
            'data' => $reviews
        ]);
    }
}

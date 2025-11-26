<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Produk;
use Illuminate\Http\Request;

class FavoritController extends Controller
{
    /**
     * Display the user's favorite products
     */
    public function index()
    {
        $favorit = Favorit::with('produk')
            ->where('pembeli_id', auth()->id())
            ->paginate(12);

        return view('movr.favorit.index', compact('favorit'));
    }

    /**
     * Toggle favorite status for a product
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
        ]);

        $produk = Produk::findOrFail($request->produk_id);

        // Jika hanya ingin cek status favorit
        if ($request->input('check_only')) {
            $isFavorite = Favorit::where('pembeli_id', auth()->id())
                ->where('produk_id', $request->produk_id)
                ->exists();

            return response()->json([
                'isfavorite' => $isFavorite
            ]);
        }

        // Cek apakah produk sudah difavoritkan sebelumnya
        $existingFavorite = Favorit::where('pembeli_id', auth()->id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existingFavorite) {
            // Hapus dari favorit
            $existingFavorite->delete();

            return response()->json([
                'status' => 'remove',
                'message' => 'Produk berhasil dihapus dari favorit',
                'isfavorite' => false
            ]);
        } else {
            // Tambahkan ke favorit
            Favorit::create([
                'pembeli_id' => auth()->id(),
                'produk_id' => $request->produk_id,
            ]);

            return response()->json([
                'status' => 'add',
                'message' => 'Produk berhasil ditambahkan ke favorit',
                'isfavorite' => true
            ]);
        }
    }
}

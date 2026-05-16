<?php

namespace App\Http\Controllers\Api;

use App\Models\Keranjang;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'detail_produk_id' => 'required|integer',
            'jumlah' => 'integer|min:1'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $detail = DetailProduk::findOrFail($request->detail_produk_id);
            
            // Validasi stok
            if ($detail->stok < $request->input('jumlah', 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup. Stok tersedia: ' . $detail->stok
                ], 422);
            }

            $penggunaId = auth()->user()->pengguna_id;
            
            // Update atau create cart item
            $cart = Keranjang::updateOrCreate(
                ['pengguna_id' => $penggunaId, 'detail_produk_id' => $detail->detail_produk_id],
                ['jumlah' => $request->input('jumlah', 1)]
            );

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart' => $cart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate(['keranjang_id' => 'required|integer','jumlah' => 'required|integer|min:1']);
        if (!auth()->check()) return response()->json(['success'=>false,'message'=>'Silakan login'],401);
        try {
            $cart = Keranjang::findOrFail($request->keranjang_id);
            if ($cart->pengguna_id != auth()->user()->pengguna_id) return response()->json(['success'=>false,'message'=>'Unauthorized'],403);
            $detail = DetailProduk::findOrFail($cart->detail_produk_id);
            if ($detail->stok < $request->jumlah) return response()->json(['success'=>false,'message'=>'Stok tidak cukup'],422);
            $cart->jumlah = $request->jumlah;
            $cart->save();
            return response()->json(['success'=>true,'cart'=>$cart]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

    public function remove($id)
    {
        if (!auth()->check()) return response()->json(['success'=>false,'message'=>'Silakan login'],401);
        try {
            $cart = Keranjang::findOrFail($id);
            if ($cart->pengguna_id != auth()->user()->pengguna_id) return response()->json(['success'=>false,'message'=>'Unauthorized'],403);
            $cart->delete();
            return response()->json(['success'=>true]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }
}

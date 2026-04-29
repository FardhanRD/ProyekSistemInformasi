<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem; // Pastikan Model di-import
use App\Models\Product;       // Pastikan Model di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    // GET: Ambil data keranjang
    public function index()
    {
        // 1. KOREKSI KRUSIAL: Gunakan 'produk' (sesuai Model Anda)
        // Jangan gunakan 'product'!
        $keranjangItems = KeranjangItem::with('produk') 
            ->where('pembeli_id', Auth::id())
            ->get();
        
        // 2. Hitung Total
        $total = $keranjangItems->sum(function ($item) {
            // Cek null safety: jika produk dihapus admin, jangan error
            if ($item->produk) {
                return $item->jumlah * $item->produk->price; // Pastikan kolom harga di tabel products adalah 'price'
            }
            return 0;
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $keranjangItems,
                'total_belanja' => $total
            ]
        ], 200);
    }

    /**
     * Tambah item ke keranjang
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $produk = Product::find($request->produk_id);
        
        // Cek ketersediaan stok produk
        if ($produk->stock < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        // Cek apakah item sudah ada di keranjang
        $existingItem = KeranjangItem::where('pembeli_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existingItem) {
            $existingItem->jumlah += $request->jumlah;
            $existingItem->save();
        } else {
            KeranjangItem::create([
                'pembeli_id' => Auth::id(),
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'harga_saat_ini' => $produk->price,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil masuk keranjang'
        ], 201);
    }

    /**
     * Update jumlah item keranjang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjangItem = KeranjangItem::where('id', $id)
            ->where('pembeli_id', Auth::id())
            ->first();

        if (!$keranjangItem) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        $produk = Product::find($keranjangItem->produk_id);
        if ($produk && $produk->stock < $request->jumlah) {
             return response()->json(['message' => 'Stok maksimum tercapai'], 400);
        }

        $keranjangItem->update([
            'jumlah' => $request->jumlah,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang diperbarui',
        ]);
    }

    /**
     * Hapus item dari keranjang
     */
    public function destroy($id)
    {
        $keranjangItem = KeranjangItem::where('id', $id)
            ->where('pembeli_id', Auth::id())
            ->first();
        
        if (!$keranjangItem) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        $keranjangItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk dihapus dari keranjang'
        ]);
    }
}
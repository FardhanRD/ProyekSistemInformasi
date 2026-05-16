<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $items = [];
        if ($user) {
            $items = Keranjang::with(['detail.produk.images'])
                ->where('pengguna_id', $user->pengguna_id)
                ->get();

        }

        return view('buyer.cart.index', compact('items'));
    }

    public function add(Request $request)
    {
        $request->validate(['detail_produk_id'=>'required|integer','jumlah'=>'integer|min:1']);
        $user = Auth::user();
        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
            }
            return redirect()->route('login');
        }

        $detail = DetailProduk::findOrFail($request->detail_produk_id);
        $cart = Keranjang::updateOrCreate(
            ['pengguna_id' => $user->pengguna_id, 'detail_produk_id' => $detail->detail_produk_id],
            ['jumlah' => $request->input('jumlah',1)]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart' => $cart,
            ]);
        }

        return back()->with('success','Produk ditambahkan ke keranjang');
    }

    public function update(Request $request)
    {
        $request->validate([
            'keranjang_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $item = Keranjang::with('detail')->findOrFail($request->keranjang_id);
        if ((int) $item->pengguna_id !== (int) $user->pengguna_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $stokMaks = (int) optional($item->detail)->stok;
        $jumlah = (int) $request->jumlah;
        if ($stokMaks > 0 && $jumlah > $stokMaks) {
            return response()->json(['success' => false, 'message' => 'Stok tidak cukup'], 422);
        }

        $item->jumlah = $jumlah;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Jumlah keranjang diperbarui',
            'cart' => $item->fresh(['detail.produk.images']),
        ]);
    }

    public function remove($id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $item = Keranjang::findOrFail($id);
        if ($item->pengguna_id != $user->pengguna_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $item->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produk dihapus dari keranjang']);
        }

        return back()->with('success','Produk dihapus dari keranjang');
    }
}

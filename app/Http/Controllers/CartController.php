<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $items = collect();

        // Security: pastikan user terautentikasi
        if (! $user) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json(['success' => true, 'data' => []]);
            }
            return view('buyer.cart.index', compact('items'));
        }

        // Ambil ownerColumn/ownerId dan gunakan untuk mengambil keranjang
        $ownerColumn = Keranjang::ownerColumn();
        $ownerId = Keranjang::resolveOwnerId($user);
        if ($ownerId) {
            $items = Keranjang::with(['detail.produk.images'])
                ->where($ownerColumn, $ownerId)
                ->get();
        }

        if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
            $formatted = collect($items)->map(function ($item) {
                $produk = optional($item->detail)->produk;
                $imageUrl = $produk && $produk->images->first() ? $produk->images->first()->url_gambar : '';
                return [
                    'id' => $item->keranjang_id,
                    'jumlah' => $item->jumlah,
                    'harga_saat_ini' => optional($item->detail)->harga ?? optional($produk)->harga_dasar ?? 0,
                    'produk' => [
                        'id' => optional($produk)->produk_id ?? 0,
                        'name' => optional($produk)->nama_produk ?? 'Tanpa Nama',
                        'price' => optional($produk)->harga_dasar ?? 0,
                        'description' => optional($produk)->deskripsi ?? '',
                        'image' => $imageUrl
                    ]
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $formatted
            ]);
        }

        return view('buyer.cart.index', compact('items'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'detail_produk_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'jumlah' => 'integer|min:1'
        ]);

        if (!$request->detail_produk_id && !$request->product_id) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'The product_id or detail_produk_id field is required.'], 422);
            }
            return back()->withErrors(['error' => 'Pilih produk terlebih dahulu']);
        }

        $user = Auth::user();
        if (! $user) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
            }
            return redirect()->route('login');
        }

        if ($request->detail_produk_id) {
            $detail = DetailProduk::findOrFail($request->detail_produk_id);
        } else {
            $detail = DetailProduk::where('produk_id', $request->product_id)->firstOrFail();
        }

        // Security + owner resolution
        $ownerColumn = Keranjang::ownerColumn();
        $ownerId = Keranjang::resolveOwnerId($user);

        $cart = Keranjang::updateOrCreate(
            [$ownerColumn => $ownerId, 'detail_produk_id' => $detail->detail_produk_id],
            ['jumlah' => $request->input('jumlah',1)]
        );

        if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart' => $cart,
            ]);
        }

        return back()->with('success','Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, $id = null)
    {
        $cartId = $id ?? $request->keranjang_id;
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $item = Keranjang::with('detail')->findOrFail($cartId);

        // Security: pastikan owner cocok
        $ownerColumn = Keranjang::ownerColumn();
        $ownerId = Keranjang::resolveOwnerId($user);
        if ((int) $item->{$ownerColumn} !== (int) $ownerId) {
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
        $ownerColumn = Keranjang::ownerColumn();
        $ownerId = Keranjang::resolveOwnerId($user);
        if ($item->{$ownerColumn} != $ownerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $item->delete();

        if (request()->expectsJson() || request()->ajax() || request()->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Produk dihapus dari keranjang']);
        }

        return back()->with('success','Produk dihapus dari keranjang');
    }
}

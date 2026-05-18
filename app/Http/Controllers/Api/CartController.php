<?php

namespace App\Http\Controllers\Api;

use App\Models\Keranjang;
use App\Models\DetailProduk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $penggunaId = auth()->user()->pengguna_id;

            $items = Keranjang::with(['detail.produk.images', 'detail.produk.details', 'detail.produk.kategori.parent'])
                ->where('pengguna_id', $penggunaId)
                ->get();

            $formatted = $items->map(function ($item) {
                $detail = $item->detail;
                $produk = optional($detail)->produk;
                if (!$produk) return null;

                $imageUrl = $produk->images->first() ? $produk->images->first()->url_gambar : '';

                return [
                    'id' => $item->keranjang_id,
                    'jumlah' => $item->jumlah,
                    'harga_saat_ini' => $detail->harga ?? $produk->harga_dasar ?? 0,
                    'produk' => [
                        'id' => $produk->produk_id,
                        'name' => $produk->nama_produk,
                        'price' => $produk->harga_dasar,
                        'description' => $produk->deskripsi ?? '',
                        'image' => $imageUrl,
                        'category' => ($produk->kategori && $produk->kategori->parent ? $produk->kategori->parent->nama_kategori . " " : "") . ($produk->kategori->nama_kategori ?? "Umum"),
                        'details' => $produk->details->map(function ($d) {
                            return [
                                'detail_id' => $d->detail_produk_id,
                                'size' => $d->ukuran,
                                'stock' => $d->stok,
                                'price' => $d->harga,
                            ];
                        })
                    ]
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'data' => $formatted
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request)
    {
        $request->validate([
            'detail_produk_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'jumlah' => 'integer|min:1'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        if (!$request->detail_produk_id && !$request->product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Harap sertakan detail_produk_id atau product_id'
            ], 422);
        }

        try {
            if ($request->detail_produk_id) {
                $detail = DetailProduk::findOrFail($request->detail_produk_id);
            } else {
                $detail = DetailProduk::where('produk_id', $request->product_id)->firstOrFail();
            }
            
            // Validasi stok
            if ($detail->stok < $request->input('jumlah', 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup. Stok tersedia: ' . $detail->stok
                ], 422);
            }

            $penggunaId = auth()->user()->pengguna_id;
            
            // Update atau create cart item
            // Menjumlahkan jumlah jika sudah ada di keranjang
            $existing = Keranjang::where('pengguna_id', $penggunaId)
                ->where('detail_produk_id', $detail->detail_produk_id)
                ->first();

            if ($existing) {
                $newJumlah = $existing->jumlah + $request->input('jumlah', 1);
                if ($detail->stok < $newJumlah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk tambahan jumlah ini'
                    ], 422);
                }
                $existing->jumlah = $newJumlah;
                $existing->save();
                $cart = $existing;
            } else {
                $cart = Keranjang::create([
                    'pengguna_id' => $penggunaId,
                    'detail_produk_id' => $detail->detail_produk_id,
                    'jumlah' => $request->input('jumlah', 1)
                ]);
            }

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $cart = Keranjang::findOrFail($id);
            if ($cart->pengguna_id != auth()->user()->pengguna_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $detail = DetailProduk::findOrFail($cart->detail_produk_id);
            if ($detail->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup. Stok tersedia: ' . $detail->stok
                ], 422);
            }

            $cart->jumlah = $request->jumlah;
            $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Jumlah produk diperbarui',
                'cart' => $cart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $cart = Keranjang::findOrFail($id);
            if ($cart->pengguna_id != auth()->user()->pengguna_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari keranjang'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

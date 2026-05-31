<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::where('is_active', 1)->with(['images', 'kategori.parent', 'details']);

        if ($request->has('type')) {
            $type = $request->input('type');
            if ($type === 'new_arrivals') {
                $orderField = \Illuminate\Support\Facades\Schema::hasColumn('produk', 'penyimpanan_waktu')
                    ? 'penyimpanan_waktu'
                    : (\Illuminate\Support\Facades\Schema::hasColumn('produk', 'created_at') ? 'created_at' : 'produk_id');
                $query->where('status_publish', 'publish')
                      ->orderByDesc($orderField)
                      ->limit(8);
            } elseif ($type === 'best_sellers') {
                $query->orderByDesc('total_terjual')
                      ->limit(8);
            }
        }

        $products = $query->get();
        
        $formatted = $products->map(function ($p) {
            return [
                'id' => $p->produk_id,
                'name' => $p->nama_produk,
                'price' => $p->harga_dasar,
                'description' => $p->deskripsi ?? '',
                'image' => $p->images->first()->url_gambar ?? '',
                'gallery' => $p->images->pluck('url_gambar')->toArray(),
                'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum"),
                'details' => $p->details->map(function ($d) {
                    return [
                        'detail_id' => $d->detail_produk_id,
                        'size' => $d->ukuran,
                        'stock' => $d->stok,
                        'price' => $d->harga,
                    ];
                })
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ], 200);
    }
    public function getByCategory($id) {
        $products = \App\Models\Produk::where('kategori_id', $id)->with(['images', 'kategori.parent', 'details'])->get();
        $formatted = $products->map(function ($p) {
            return [
                'id' => $p->produk_id,
                'name' => $p->nama_produk,
                'price' => $p->harga_dasar,
                'description' => $p->deskripsi ?? '',
                'image' => $p->images->first()->url_gambar ?? '',
                'gallery' => $p->images->pluck('url_gambar')->toArray(),
                'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum"),
                'details' => $p->details->map(function ($d) {
                    return [
                        'detail_id' => $d->detail_produk_id,
                        'size' => $d->ukuran,
                        'stock' => $d->stok,
                        'price' => $d->harga,
                    ];
                })
            ];
        });
        return response()->json(['status'=>'success', 'data'=>$formatted], 200);
    }

    public function show($id)
    {
        $p = Produk::with(['images', 'kategori.parent', 'details'])->find($id);
        if (!$p) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
        }
        
        $formatted = [
            'id' => $p->produk_id,
            'name' => $p->nama_produk,
            'price' => $p->harga_dasar,
            'description' => $p->deskripsi ?? '',
            'image' => $p->images->first()->url_gambar ?? '',
            'gallery' => $p->images->pluck('url_gambar')->toArray(),
            'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum"),
            'details' => $p->details->map(function ($d) {
                return [
                    'detail_id' => $d->detail_produk_id,
                    'size' => $d->ukuran,
                    'stock' => $d->stok,
                    'price' => $d->harga,
                ];
            })
        ];

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ], 200);
    }
}
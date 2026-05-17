<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Produk::with(['images', 'kategori.parent', 'details'])->get();
        
        $formatted = $products->map(function ($p) {
            return [
                'id' => $p->produk_id,
                'name' => $p->nama_produk,
                'price' => $p->harga_dasar,
                'description' => $p->deskripsi ?? '',
                'image' => $p->images->first()->url_gambar ?? '',
                'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum")
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
                'category' => ($p->kategori && $p->kategori->parent ? $p->kategori->parent->nama_kategori . " " : "") . ($p->kategori->nama_kategori ?? "Umum")
            ];
        });
        return response()->json(['status'=>'success', 'data'=>$formatted], 200);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        // Try fulltext (if index exists) otherwise fallback to LIKE
        $results = Produk::where(function($w) use ($q) {
            $w->where('nama_produk', 'like', "%{$q}%")
              ->orWhere('deskripsi', 'like', "%{$q}%");
        })
        ->where('is_active', 1)
        ->limit(5)
        ->get(['produk_id','nama_produk','slug','harga_dasar']);

        $out = $results->map(function($p){
            return [
                'produk_id' => $p->produk_id,
                'nama_produk' => $p->nama_produk,
                'slug' => $p->slug,
                'harga' => $p->harga_dasar,
            ];
        });

        return response()->json($out);
    }
}

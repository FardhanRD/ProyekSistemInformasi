<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Produk;
use Illuminate\Http\Request;
class WishlistController extends Controller {
    public function index() {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        $wishlists = Wishlist::with('produk.images')->where('pengguna_id', $pengguna_id)->get();
        
        $data = $wishlists->map(function($w) {
            $p = $w->produk;
            if(!$p) return null;
            return [
                'id' => $w->wishlist_id,
                'product_id' => $p->produk_id,
                'name' => $p->nama_produk,
                'price' => $p->harga_dasar,
                'image' => $p->images->first()->url_gambar ?? ''
            ];
        })->filter()->values();
        return response()->json(['status'=>'success', 'data'=>$data], 200);
    }
    public function store(Request $request) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        $exists = Wishlist::where('pengguna_id', $pengguna_id)->where('produk_id', $request->product_id)->exists();
        if(!$exists) {
            Wishlist::create(['pengguna_id'=>$pengguna_id, 'produk_id'=>$request->product_id]);
        }
        return response()->json(['status'=>'success', 'message'=>'Ditambahkan'], 201);
    }
    public function check($product_id) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        $exists = Wishlist::where('pengguna_id', $pengguna_id)->where('produk_id', $product_id)->exists();
        return response()->json(['is_favorited'=>$exists], 200);
    }
    public function destroy($id) {
        Wishlist::where('wishlist_id', $id)->delete();
        return response()->json(['status'=>'success'], 200);
    }
    public function destroyByProduct($product_id) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        Wishlist::where('pengguna_id', $pengguna_id)->where('produk_id', $product_id)->delete();
        return response()->json(['status'=>'success'], 200);
    }
    public function clear() {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        Wishlist::where('pengguna_id', $pengguna_id)->delete();
        return response()->json(['status'=>'success'], 200);
    }
}
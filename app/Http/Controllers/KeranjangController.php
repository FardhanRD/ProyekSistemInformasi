<?php

namespace App\Http\Controllers;

use App\Models\KeranjangItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    // Menampilkan Keranjang
    public function index()
    {
        $keranjangItems = KeranjangItem::with('produk')
            ->where('pembeli_id', Auth::id())
            ->get();
        
        $total = $keranjangItems->sum(function ($item) {
            return $item->jumlah * $item->harga_saat_ini;
        });
        
        return view('movr.keranjang.index', compact('keranjangItems', 'total'));
    }

    // Menambah ke Keranjang
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produk = Product::findOrFail($request->produk_id);
        
        if ($produk->stock < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // Jika user menekan tombol 'Buy Now' (action=checkout), lewati redirect ke keranjang
        if ($request->input('action') === 'checkout') {
            $checkoutItem = [
                'id' => $produk->id,
                'name' => $produk->name,
                'price' => $produk->price,
                'image' => $produk->image,
                'qty' => (int) $request->jumlah,
                'total' => $produk->price * (int) $request->jumlah,
            ];

            session(['checkout_type' => 'direct']);
            session(['checkout_items' => [$checkoutItem]]);

            return redirect()->route('checkout.index');
        }

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

        return redirect()->route('keranjang.index')->with('status', 'Produk berhasil masuk keranjang!');
    }

    // Update Keranjang
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjangItem = KeranjangItem::where('id', $id)
            ->where('pembeli_id', Auth::id())
            ->firstOrFail();
            
        $produk = Product::findOrFail($keranjangItem->produk_id);
        
        if ($produk->stock < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok maksimum tercapai!');
        }

        $keranjangItem->update([
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('keranjang.index')->with('status', 'Keranjang diperbarui!');
    }

    // Hapus dari Keranjang
    public function destroy($id)
    {
        $keranjangItem = KeranjangItem::where('id', $id)
            ->where('pembeli_id', Auth::id())
            ->firstOrFail();
        
        $keranjangItem->delete();

        return redirect()->route('keranjang.index')->with('status', 'Produk dihapus!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('detail_produk') || !Schema::hasTable('produk')) {
            return view('admin.pricing.index', [
                'variants' => collect(),
                'produk_list' => collect(),
                'supplier_list' => collect(),
            ]);
        }

        $produk_id = $request->get('produk_id');
        $supplier_id = $request->get('supplier_id');

        $variants = DetailProduk::with(['produk'])
            ->when($produk_id, fn($q) => $q->where('produk_id', $produk_id))
            ->orderBy('produk_id')
            ->orderBy('detail_produk_id')
            ->paginate(20)
            ->withQueryString();

        $produk_list = Produk::where('is_active', 1)
            ->orderBy('nama_produk')
            ->get();

        $supplier_list = Supplier::where('is_verified', 1)
            ->orderBy('nama_toko')
            ->get();

        return view('admin.pricing.index', [
            'variants' => $variants,
            'produk_list' => $produk_list,
            'supplier_list' => $supplier_list,
            'produk_filter' => $produk_id,
            'supplier_filter' => $supplier_id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'harga' => 'required|numeric|min:0',
        ]);

        $variant = DetailProduk::findOrFail($id);
        $variant->update(['harga' => $request->get('harga')]);

        return response()->json(['success' => true, 'message' => 'Harga berhasil diperbarui.']);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:detail_produk,detail_produk_id',
            'harga' => 'required|numeric|min:0',
        ]);

        DetailProduk::whereIn('detail_produk_id', $request->get('variant_ids'))
            ->update(['harga' => $request->get('harga')]);

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Harga untuk ' . count($request->get('variant_ids')) . ' variant berhasil diperbarui.');
    }
}

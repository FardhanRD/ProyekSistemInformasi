<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\ProdukSupplier;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupplierProductController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('produk_supplier') || !Schema::hasTable('produk') || !Schema::hasTable('supplier')) {
            return view('admin.supplier-product.index', [
                'relations' => collect(),
                'supplier_list' => collect(),
                'produk_list' => collect(),
            ]);
        }

        $supplier_id = $request->get('supplier_id');
        $produk_id = $request->get('produk_id');

        $relations = ProdukSupplier::with(['supplier', 'produk'])
            ->when($supplier_id, fn($q) => $q->where('supplier_id', $supplier_id))
            ->when($produk_id, fn($q) => $q->where('produk_id', $produk_id))
            ->orderBy('supplier_id')
            ->orderBy('produk_id')
            ->paginate(20)
            ->withQueryString();

        $supplier_list = Supplier::where('is_verified', 1)
            ->orderBy('nama_toko')
            ->get();

        $produk_list = Produk::where('is_active', 1)
            ->orderBy('nama_produk')
            ->get();

        return view('admin.supplier-product.index', [
            'relations' => $relations,
            'supplier_list' => $supplier_list,
            'produk_list' => $produk_list,
            'supplier_filter' => $supplier_id,
            'produk_filter' => $produk_id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,supplier_id',
            'produk_id' => 'required|exists:produk,produk_id',
            'harga_modal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Check if relation already exists
        $existing = ProdukSupplier::where('supplier_id', $request->get('supplier_id'))
            ->where('produk_id', $request->get('produk_id'))
            ->first();

        if ($existing) {
            return redirect()->route('admin.supplier-product.index')
                ->with('error', 'Relasi supplier-produk ini sudah ada.');
        }

        ProdukSupplier::create([
            'supplier_id' => $request->get('supplier_id'),
            'produk_id' => $request->get('produk_id'),
            'harga_modal' => $request->get('harga_modal'),
            'catatan' => $request->get('catatan'),
        ]);

        return redirect()->route('admin.supplier-product.index')
            ->with('success', 'Relasi supplier-produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'harga_modal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $relation = ProdukSupplier::findOrFail($id);
        $relation->update($request->only(['harga_modal', 'catatan']));

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $relation = ProdukSupplier::findOrFail($id);
        $relation->delete();

        return redirect()->route('admin.supplier-product.index')
            ->with('success', 'Relasi supplier-produk berhasil dihapus.');
    }
}

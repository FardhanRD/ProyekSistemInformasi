<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('detail_produk') || !Schema::hasTable('produk')) {
            return view('admin.stock.index', [
                'variants' => collect(),
                'produk_list' => collect(),
                'low_stock_count' => 0,
            ]);
        }

        $produk_id = $request->get('produk_id');
        $status = $request->get('status');

        $variants = DetailProduk::with(['produk'])
            ->when($produk_id, fn($q) => $q->where('produk_id', $produk_id))
            ->when($status, function($q) use ($status) {
                if ($status === 'low') {
                    return $q->whereRaw('stok <= stok_minimum');
                } elseif ($status === 'out') {
                    return $q->where('stok', 0);
                } elseif ($status === 'ok') {
                    return $q->whereRaw('stok > stok_minimum');
                }
            })
            ->orderBy('produk_id')
            ->orderBy('detail_produk_id')
            ->paginate(20)
            ->withQueryString();

        $produk_list = Produk::where('is_active', 1)
            ->orderBy('nama_produk')
            ->get();

        $low_stock_count = Schema::hasTable('detail_produk')
            ? DetailProduk::whereRaw('stok <= stok_minimum')->count()
            : 0;

        return view('admin.stock.index', [
            'variants' => $variants,
            'produk_list' => $produk_list,
            'low_stock_count' => $low_stock_count,
            'produk_filter' => $produk_id,
            'status_filter' => $status,
        ]);
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'detail_produk_id' => 'required|exists:detail_produk,detail_produk_id',
            'qty' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        $variant = DetailProduk::findOrFail($request->get('detail_produk_id'));
        $stok_sebelum = $variant->stok;
        $qty_adjustment = $request->get('qty');
        $stok_sesudah = $stok_sebelum + $qty_adjustment;

        if ($stok_sesudah < 0) {
            return redirect()->back()->with('error', 'Stok tidak boleh negatif.');
        }

        // Update stok di detail_produk
        $variant->update(['stok' => $stok_sesudah]);

        // Log ke stock_movement
        if (Schema::hasTable('stock_movement')) {
            StockMovement::create([
                'detail_produk_id' => $variant->detail_produk_id,
                'jenis' => $qty_adjustment > 0 ? 'in' : 'out',
                'qty' => abs($qty_adjustment),
                'stok_sebelum' => $stok_sebelum,
                'stok_sesudah' => $stok_sesudah,
                'referensi' => 'ADJUSTMENT',
                'catatan' => $request->get('catatan', 'Manual adjustment'),
                'dibuat_oleh' => auth()->user()->id ?? null,
            ]);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stok berhasil disesuaikan. (' . ($qty_adjustment > 0 ? '+' : '') . $qty_adjustment . ')');
    }
}

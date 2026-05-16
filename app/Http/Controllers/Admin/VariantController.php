<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\WarnaProduk;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        // Total variants + stats
        if (!Schema::hasTable('detail_produk')) {
            $variants = collect();
            $total_variants = 0;
        } else {
            $search = $request->get('q');
            $color = $request->get('color');
            $size = $request->get('size');
            $status = $request->get('status');

            $baseQuery = DetailProduk::query()
                ->with(['produk'])
                ->when($search, fn($q) => $q->whereHas('produk', fn($sq) => $sq->where('nama_produk', 'like', "%{$search}%")))
                ->when($size, fn($q) => $q->where('ukuran', $size))
                ->orderBy('detail_produk_id', 'desc');

            $variants = $baseQuery->paginate(20);
            $total_variants = (clone $baseQuery)->count();
        }

        // Unique colors - disabled since warna_id column doesn't exist in detail_produk
        $unique_colors = collect();

        // Unique sizes
        $unique_sizes = collect();
        if (Schema::hasTable('detail_produk')) {
            $unique_sizes = DetailProduk::distinct()
                ->whereNotNull('ukuran')
                ->pluck('ukuran')
                ->map(fn($s) => ['id' => $s, 'nama' => $s])
                ->unique('id');
        }

        // Low stock alert count
        $low_stock_alert = 0;
        if (Schema::hasTable('detail_produk')) {
            $low_stock_alert = DetailProduk::whereRaw('stok < 5')->count();
        }

        // Last sync timestamp
        $last_sync = now();

        return view('admin.variant.index', [
            'variants' => $variants,
            'total_variants' => $total_variants,
            'unique_colors' => $unique_colors,
            'unique_sizes' => $unique_sizes,
            'low_stock_alert' => $low_stock_alert,
            'last_sync' => $last_sync,
            'search' => $search,
            'color' => $color,
            'size' => $size,
            'status' => $status,
        ]);
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $startDate = $start ? date('Y-m-d', strtotime($start)) : null;
        $endDate = $end ? date('Y-m-d', strtotime($end)) : null;

        $events = [];

        if (Schema::hasTable('detail_produk')) {
            $variants = DetailProduk::query();
            // Note: detail_produk table doesn't have created_at/updated_at timestamps
            // So date-based filtering is not available
            $variants = $variants->selectRaw('detail_produk_id, COUNT(*) as count')
                ->groupBy('detail_produk_id')
                ->get();

            foreach ($variants as $row) {
                $events[] = [
                    'date' => now()->toDateString(),
                    'type' => 'new',
                    'count' => $row->count,
                ];
            }
        }

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'type' => 'required|in:color,size',
            'value' => 'required|string',
        ]);

        if ($request->get('type') === 'color') {
            $warna = WarnaProduk::create([
                'nama_warna' => $request->get('value'),
                'kode_hex' => '#000000', // placeholder
            ]);

            DetailProduk::create([
                'produk_id' => $request->get('produk_id'),
                'warna_id' => $warna->warna_id,
                'ukuran' => null,
                'stok_total' => 0,
                'stok_minimum' => 0,
                'harga_pokok' => 0,
                'status_stok' => 'available',
            ]);
        } elseif ($request->get('type') === 'size') {
            DetailProduk::create([
                'produk_id' => $request->get('produk_id'),
                'warna_id' => null,
                'ukuran' => $request->get('value'),
                'stok_total' => 0,
                'stok_minimum' => 0,
                'harga_pokok' => 0,
                'status_stok' => 'available',
            ]);
        }

        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, $id)
    {
        $variant = DetailProduk::findOrFail($id);

        $request->validate([
            'stok_total' => 'numeric|min:0',
            'harga_pokok' => 'numeric|min:0',
            'status_stok' => 'in:available,low,out_of_stock',
        ]);

        $variant->update($request->only([
            'stok_total',
            'stok_minimum',
            'harga_pokok',
            'status_stok',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $variant = DetailProduk::findOrFail($id);
        $variant->delete();

        return response()->json(['success' => true]);
    }
}

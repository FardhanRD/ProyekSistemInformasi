<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\WarnaProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        $products = collect();
        $selectedProductId = null;
        $selectedProduct = null;
        $existingVariants = collect();

        if (Schema::hasTable('produk')) {
            $products = Produk::query()
                ->with(['kategori', 'supplier', 'gambarUtama', 'detailProduk'])
                ->where('is_active', 1)
                ->orderBy('nama_produk')
                ->get();

            $selectedProductId = $request->get('produk_id');

            if ($selectedProductId) {
                $selectedProduct = Produk::query()
                    ->with(['kategori', 'supplier'])
                    ->find($selectedProductId);
            }

            if (Schema::hasTable('detail_produk') && $selectedProductId) {
                $existingVariants = DetailProduk::query()
                    ->with(['warna'])
                    ->where('produk_id', $selectedProductId)
                    ->orderByDesc('detail_produk_id')
                    ->get()
                    ->map(function (DetailProduk $variant) {
                        return [
                            'id' => $variant->detail_produk_id,
                            'warna' => $variant->warna?->nama_warna ?? ($variant->nama_produk ?? ''),
                            'kode_hex' => $variant->warna?->kode_hex ?? '#000000',
                            'ukuran' => (string) ($variant->ukuran ?? ''),
                            'stok' => (int) ($variant->stok ?? 0),
                            'harga' => (float) ($variant->harga ?? 0),
                            'sku' => (string) ($variant->sku ?? ''),
                            'stok_minimum' => (int) ($variant->stok_minimum ?? 5),
                        ];
                    })
                    ->values();
            }
        }

        // If AJAX request for variants JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['variants' => $existingVariants]);
        }

        return view('admin.variant.index', [
            'products' => $products,
            'selectedProductId' => $selectedProductId,
            'selectedProduct' => $selectedProduct,
            'existingVariants' => $existingVariants,
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
        $data = $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'warna' => 'required|string|max:50',
            'kode_hex' => 'nullable|string|max:20',
            'ukuran' => 'required|string|max:20',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:1',
            'sku' => 'nullable|string|max:100',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        $warnaRecord = WarnaProduk::firstOrCreate(
            ['nama_warna' => trim($data['warna'])],
            ['kode_hex' => $data['kode_hex'] ?? '#000000']
        );

        $duplikat = DetailProduk::query()
            ->where('produk_id', $data['produk_id'])
            ->where('warna_id', $warnaRecord->warna_id)
            ->where('ukuran', $data['ukuran'])
            ->exists();

        if ($duplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Varian ' . $data['warna'] . ' ukuran ' . $data['ukuran'] . ' sudah ada!',
            ], 422);
        }

        $produk = Produk::findOrFail($data['produk_id']);
        $sku = trim((string) ($data['sku'] ?? ''));

        if ($sku === '') {
            $warnaSlug = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['warna']), 0, 3));
            $ukuranSlug = strtoupper(preg_replace('/\s+/', '', $data['ukuran']));
            $sku = 'SKU-' . str_pad((string) $produk->produk_id, 3, '0', STR_PAD_LEFT) . '-' . ($warnaSlug ?: 'VAR') . '-' . ($ukuranSlug ?: 'STD');
        }

        $detail = new DetailProduk();
        $detail->forceFill([
            'produk_id' => $produk->produk_id,
            'warna_id' => $warnaRecord->warna_id,
            'nama_produk' => $produk->nama_produk,
            'ukuran' => $data['ukuran'],
            'stok' => $data['stok'],
            'harga' => $data['harga'],
            'sku' => $sku,
            'stok_minimum' => $data['stok_minimum'] ?? 5,
            'is_active' => 1,
        ]);
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Varian berhasil disimpan',
            'variant' => [
                'id' => $detail->detail_produk_id,
                'warna' => $data['warna'],
                'kode_hex' => $data['kode_hex'] ?? '#000000',
                'ukuran' => $data['ukuran'],
                'stok' => (int) $data['stok'],
                'harga' => (float) $data['harga'],
                'sku' => $sku,
                'stok_minimum' => (int) ($data['stok_minimum'] ?? 5),
            ],
        ]);
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

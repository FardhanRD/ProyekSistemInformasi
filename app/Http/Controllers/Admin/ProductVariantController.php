<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\MasterProduct;
use App\Models\ProductVariant;
use App\Services\AdminLogger;
use App\Services\SkuGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    public function index(MasterProduct $masterProduct)
    {
        $data = $masterProduct->variants()->with(['activePrice', 'inventory'])->orderBy('size')->orderBy('color')->get();
        return view('movr.admin.master_products.variants', compact('data', 'masterProduct'));
    }

    public function store(Request $request, MasterProduct $masterProduct, SkuGeneratorService $skuGenerator, AdminLogger $logger)
    {
        $validated = $request->validate([
            'size' => 'required|string|max:20',
            'color' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'is_active' => 'nullable|boolean',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $validated['master_product_id'] = $masterProduct->id;
        $validated['sku'] = $validated['sku'] ?? $skuGenerator->generate($masterProduct, $validated['size'], $validated['color']);

        $variant = ProductVariant::create($validated);
        InventoryItem::firstOrCreate([
            'product_variant_id' => $variant->id,
        ], [
            'quantity' => 0,
            'min_stock' => (int) ($validated['min_stock'] ?? 5),
        ]);

        $logger->logActivity(auth()->id(), 'product_variant', 'create', 'Tambah varian produk', ['id' => $variant->id]);

        return response()->json($variant->load(['inventory', 'activePrice']), 201);
    }

    public function bulkGenerate(Request $request, MasterProduct $masterProduct, SkuGeneratorService $skuGenerator, AdminLogger $logger)
    {
        $validated = $request->validate([
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'required|string|max:20',
            'colors' => 'required|array|min:1',
            'colors.*' => 'required|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $created = [];

        DB::transaction(function () use ($validated, $masterProduct, $skuGenerator, &$created) {
            foreach ($validated['sizes'] as $size) {
                foreach ($validated['colors'] as $color) {
                    $variant = ProductVariant::firstOrCreate([
                        'master_product_id' => $masterProduct->id,
                        'size' => $size,
                        'color' => $color,
                    ], [
                        'sku' => $skuGenerator->generate($masterProduct, $size, $color),
                        'is_active' => true,
                    ]);

                    InventoryItem::firstOrCreate([
                        'product_variant_id' => $variant->id,
                    ], [
                        'quantity' => 0,
                        'min_stock' => (int) ($validated['min_stock'] ?? 5),
                    ]);

                    $created[] = $variant;
                }
            }
        });

        $logger->logActivity(auth()->id(), 'product_variant', 'bulk_generate', 'Generate varian SKU otomatis', [
            'master_product_id' => $masterProduct->id,
            'generated_count' => count($created),
        ]);

        return response()->json(['message' => 'Varian berhasil digenerate', 'data' => $created]);
    }

    public function update(Request $request, ProductVariant $variant, AdminLogger $logger)
    {
        $validated = $request->validate([
            'size' => 'sometimes|required|string|max:20',
            'color' => 'sometimes|required|string|max:50',
            'sku' => 'sometimes|required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'is_active' => 'nullable|boolean',
        ]);

        $before = $variant->toArray();
        $variant->update($validated);

        $logger->logActivity(auth()->id(), 'product_variant', 'update', 'Update varian produk', ['id' => $variant->id]);
        $logger->logAudit(auth()->id(), 'update', $variant, $before, $variant->fresh()->toArray());

        return response()->json($variant->fresh()->load(['inventory', 'activePrice']));
    }

    public function destroy(ProductVariant $variant, AdminLogger $logger)
    {
        $before = $variant->toArray();
        $variant->delete();

        $logger->logActivity(auth()->id(), 'product_variant', 'delete', 'Hapus varian produk', ['id' => $before['id'] ?? null]);

        return response()->json(['message' => 'Varian dihapus']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\SupplierProduct;
use App\Services\AdminLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function dashboard()
    {
        $total_variants_tracked = InventoryItem::count();
        $low_stock_items = InventoryItem::with('variant.masterProduct')
            ->whereColumn('quantity', '<=', 'min_stock')
            ->orderBy('quantity')
            ->get();

        $low_stock_count = $low_stock_items->count();
        
        return view('movr.admin.inventory.dashboard', compact('total_variants_tracked', 'low_stock_count', 'low_stock_items'));
    }

    public function linkSupplierProduct(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'master_product_id' => 'required|exists:master_products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'purchase_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $supplierProduct = SupplierProduct::updateOrCreate([
            'supplier_id' => $validated['supplier_id'],
            'master_product_id' => $validated['master_product_id'],
            'product_variant_id' => $validated['product_variant_id'] ?? null,
        ], [
            'purchase_price' => $validated['purchase_price'],
            'stock' => (int) ($validated['stock'] ?? 0),
            'min_stock' => (int) ($validated['min_stock'] ?? 5),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $logger->logActivity(auth()->id(), 'supplier_product', 'upsert', 'Link supplier ke produk/varian', ['id' => $supplierProduct->id]);

        return response()->json($supplierProduct->load(['supplier', 'masterProduct', 'variant']));
    }

    public function adjust(Request $request, ProductVariant $variant, InventoryService $inventoryService, AdminLogger $logger)
    {
        $validated = $request->validate([
            'new_qty' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $inventory = $inventoryService->adjustStock($variant, (int) $validated['new_qty'], auth()->id(), $validated['note'] ?? null);

        $logger->logActivity(auth()->id(), 'inventory', 'adjust', 'Penyesuaian stok manual', [
            'variant_id' => $variant->id,
            'new_qty' => $validated['new_qty'],
        ]);

        return response()->json($inventory->fresh());
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with('variant.masterProduct', 'admin')->latest();

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        $data = $query->paginate(20);
        return view('movr.admin.inventory.movements', compact('data'));
    }
}

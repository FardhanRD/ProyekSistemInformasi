<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SupplierProduct;
use App\Services\AdminLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.variant.masterProduct'])->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        $data = $query->paginate(20);
        return view('movr.admin.purchases.index', compact('data'));
    }

    public function store(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = DB::transaction(function () use ($validated) {
            $sequence = str_pad((string) (PurchaseOrder::count() + 1), 4, '0', STR_PAD_LEFT);
            $invoice = 'PO-' . now()->format('Ymd') . '-' . $sequence;

            $po = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $invoice,
                'order_date' => $validated['order_date'],
                'status' => 'ordered',
                'subtotal' => 0,
                'total' => 0,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $lineSubtotal = ((int) $item['qty']) * ((float) $item['purchase_price']);
                $po->items()->create([
                    'product_variant_id' => $item['product_variant_id'],
                    'qty' => $item['qty'],
                    'purchase_price' => $item['purchase_price'],
                    'subtotal' => $lineSubtotal,
                ]);
                $subtotal += $lineSubtotal;
            }

            $po->update(['subtotal' => $subtotal, 'total' => $subtotal]);

            return $po;
        });

        $logger->logActivity(auth()->id(), 'purchase_order', 'create', 'Buat transaksi pembelian', ['id' => $purchaseOrder->id]);

        return response()->json($purchaseOrder->load('items.variant.masterProduct'), 201);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        return response()->json($purchaseOrder->load(['supplier', 'items.variant.masterProduct']));
    }

    public function receive(PurchaseOrder $purchaseOrder, InventoryService $inventoryService, AdminLogger $logger)
    {
        if ($purchaseOrder->status === 'received') {
            return response()->json(['message' => 'Purchase order sudah diterima sebelumnya'], 422);
        }

        DB::transaction(function () use ($purchaseOrder, $inventoryService) {
            foreach ($purchaseOrder->items as $item) {
                $inventoryService->increaseStock(
                    $item->variant,
                    (int) $item->qty,
                    'purchase_order',
                    $purchaseOrder->id,
                    auth()->id(),
                    'Stock in dari penerimaan purchase order'
                );

                SupplierProduct::where('supplier_id', $purchaseOrder->supplier_id)
                    ->where('product_variant_id', $item->product_variant_id)
                    ->update([
                        'purchase_price' => $item->purchase_price,
                        'stock' => DB::raw('stock + ' . (int) $item->qty),
                    ]);
            }

            $purchaseOrder->update(['status' => 'received']);
        });

        $logger->logActivity(auth()->id(), 'purchase_order', 'receive', 'Penerimaan barang pembelian', ['id' => $purchaseOrder->id]);

        return response()->json(['message' => 'Barang diterima, stok bertambah otomatis']);
    }
}

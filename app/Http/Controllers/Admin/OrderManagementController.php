<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminLogger;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'items.variant.masterProduct'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return response()->json($query->paginate(20));
    }

    public function show(Order $order)
    {
        return response()->json($order->load(['user', 'items.product', 'items.variant.masterProduct']));
    }

    public function updateStatus(Request $request, Order $order, AdminLogger $logger)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,paid',
        ]);

        $order->update(['status' => $validated['status']]);

        $logger->logActivity(auth()->id(), 'order', 'status_update', 'Perubahan status order', [
            'order_id' => $order->id,
            'status' => $validated['status'],
        ]);

        return response()->json($order->fresh());
    }

    public function verifyPayment(Order $order, InventoryService $inventoryService, AdminLogger $logger)
    {
        if ($order->stock_reduced_at !== null) {
            return response()->json(['message' => 'Pembayaran sudah diverifikasi dan stok sudah dipotong.'], 422);
        }

        DB::transaction(function () use ($order, $inventoryService) {
            foreach ($order->items as $item) {
                if ($item->product_variant_id !== null && $item->variant) {
                    $inventoryService->decreaseStock(
                        $item->variant,
                        (int) $item->quantity,
                        'order',
                        $order->id,
                        auth()->id(),
                        'Pengurangan stok karena pembayaran diverifikasi'
                    );
                }
            }

            $invoiceNumber = $order->invoice_number ?: ('INV-' . now()->format('Ymd') . '-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));

            $order->update([
                'status' => 'paid',
                'verified_paid_at' => now(),
                'stock_reduced_at' => now(),
                'invoice_number' => $invoiceNumber,
            ]);
        });

        $logger->logActivity(auth()->id(), 'order', 'verify_payment', 'Verifikasi pembayaran dan trigger stok keluar', ['order_id' => $order->id]);

        return response()->json([
            'message' => 'Pembayaran diverifikasi, stok berkurang otomatis, invoice digenerate.',
            'invoice_number' => $order->fresh()->invoice_number,
        ]);
    }

    public function updateShipping(Request $request, Order $order, AdminLogger $logger)
    {
        $validated = $request->validate([
            'courier_service' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipping_status' => 'required|in:pending,packed,shipped,in_transit,delivered,returned',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $order->update([
            'courier_service' => $validated['courier_service'],
            'tracking_number' => $validated['tracking_number'],
            'shipping_status' => $validated['shipping_status'],
            'shipping_cost' => $validated['shipping_cost'] ?? $order->shipping_cost,
            'status' => $validated['shipping_status'] === 'delivered' ? 'delivered' : $order->status,
        ]);

        $logger->logActivity(auth()->id(), 'order', 'shipping_update', 'Update data logistik order', ['order_id' => $order->id]);

        return response()->json($order->fresh());
    }
}

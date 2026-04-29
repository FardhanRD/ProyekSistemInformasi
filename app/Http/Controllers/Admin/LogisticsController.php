<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Services\AdminLogger;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function shippingSettings()
    {
        $data = ShippingSetting::latest()->paginate(20);
        return view('movr.admin.logistics.shipping-settings', compact('data'));
    }

    public function storeShippingSetting(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'destination_zone' => 'required|string|max:255',
            'courier_service' => 'required|string|max:100',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $setting = ShippingSetting::create($validated);
        $logger->logActivity(auth()->id(), 'logistics', 'create_shipping_setting', 'Tambah pengaturan ongkir', ['setting_id' => $setting->id]);

        return response()->json($setting, 201);
    }

    public function updateOrderTracking(Request $request, Order $order, AdminLogger $logger)
    {
        $validated = $request->validate([
            'courier_service' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipping_status' => 'required|in:pending,packed,shipped,in_transit,delivered,returned',
        ]);

        $order->update([
            'courier_service' => $validated['courier_service'],
            'tracking_number' => $validated['tracking_number'],
            'shipping_status' => $validated['shipping_status'],
            'status' => $validated['shipping_status'] === 'delivered' ? 'delivered' : $order->status,
        ]);

        $logger->logActivity(auth()->id(), 'logistics', 'update_tracking', 'Update resi dan tracking', ['order_id' => $order->id]);

        return response()->json($order->fresh());
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductDiscount;
use App\Models\Voucher;
use App\Services\AdminLogger;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function vouchers()
    {
        $data = Voucher::latest()->paginate(20);
        return view('movr.admin.promo.vouchers', compact('data'));
    }

    public function storeVoucher(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'is_active' => 'nullable|boolean',
        ]);

        $voucher = Voucher::create($validated);
        $logger->logActivity(auth()->id(), 'promo', 'create_voucher', 'Buat voucher promo', ['voucher_id' => $voucher->id]);

        return response()->json($voucher, 201);
    }

    public function discounts()
    {
        return response()->json(ProductDiscount::with(['masterProduct', 'variant'])->latest()->paginate(20));
    }

    public function storeDiscount(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'master_product_id' => 'nullable|exists:master_products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'is_flash_sale' => 'nullable|boolean',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['master_product_id']) && empty($validated['product_variant_id'])) {
            return response()->json(['message' => 'Harus pilih target master product atau variant'], 422);
        }

        $discount = ProductDiscount::create($validated);

        $logger->logActivity(auth()->id(), 'promo', 'create_discount', 'Buat diskon produk/flash sale', ['discount_id' => $discount->id]);

        return response()->json($discount, 201);
    }
}

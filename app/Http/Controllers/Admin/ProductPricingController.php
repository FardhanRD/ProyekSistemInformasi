<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Services\AdminLogger;
use Illuminate\Http\Request;

class ProductPricingController extends Controller
{
    public function index(ProductVariant $variant)
    {
        $data = $variant->prices()->latest()->get();
        return view('movr.admin.master_products.pricing', compact('data', 'variant'));
    }

    public function store(Request $request, ProductVariant $variant, AdminLogger $logger)
    {
        $validated = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_start' => 'nullable|date',
            'flash_sale_end' => 'nullable|date|after_or_equal:flash_sale_start',
            'is_active' => 'nullable|boolean',
        ]);

        ProductVariantPrice::where('product_variant_id', $variant->id)->update(['is_active' => false]);

        $price = ProductVariantPrice::create([
            ...$validated,
            'product_variant_id' => $variant->id,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $logger->logActivity(auth()->id(), 'pricing', 'create', 'Set harga varian', [
            'variant_id' => $variant->id,
            'price_id' => $price->id,
        ]);

        return response()->json([
            'message' => 'Harga berhasil disimpan',
            'data' => $price,
            'effective_price' => $price->resolveEffectivePrice(),
        ], 201);
    }
}

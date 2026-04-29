<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterProduct;
use App\Services\AdminLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterProductController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterProduct::with(['category', 'variants.activePrice']);

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('sport_type', 'like', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->boolean('is_active'));
        }

        $data = $query->latest()->paginate(15);
        return view('movr.admin.master_products.variants', compact('data'));
    }

    public function store(Request $request, AdminLogger $logger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'required|string|max:255',
            'specifications' => 'nullable|array',
            'gender' => 'required|in:unisex,male,female,kids',
            'sport_type' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(6));
        $product = MasterProduct::create($validated);

        $logger->logActivity(auth()->id(), 'master_product', 'create', 'Buat master produk baru', ['id' => $product->id]);
        $logger->logAudit(auth()->id(), 'create', $product, null, $product->toArray());

        return response()->json($product->load('category'), 201);
    }

    public function show(MasterProduct $masterProduct)
    {
        return response()->json($masterProduct->load(['category', 'variants.activePrice', 'media']));
    }

    public function update(Request $request, MasterProduct $masterProduct, AdminLogger $logger)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'sometimes|required|string|max:255',
            'specifications' => 'nullable|array',
            'gender' => 'sometimes|required|in:unisex,male,female,kids',
            'sport_type' => 'sometimes|required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $before = $masterProduct->toArray();
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(6));
        }

        $masterProduct->update($validated);

        $logger->logActivity(auth()->id(), 'master_product', 'update', 'Update master produk', ['id' => $masterProduct->id]);
        $logger->logAudit(auth()->id(), 'update', $masterProduct, $before, $masterProduct->fresh()->toArray());

        return response()->json($masterProduct->fresh()->load('category'));
    }

    public function destroy(MasterProduct $masterProduct, AdminLogger $logger)
    {
        $before = $masterProduct->toArray();
        $masterProduct->delete();

        $logger->logActivity(auth()->id(), 'master_product', 'delete', 'Hapus master produk', ['id' => $before['id'] ?? null]);
        $logger->logAudit(auth()->id(), 'delete', new MasterProduct($before), $before, null);

        return response()->json(['message' => 'Master produk dihapus']);
    }
}

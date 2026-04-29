<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Ambil semua produk dengan fitur pencarian dan filter
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Pencarian berdasarkan nama produk
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Muat relasi kategori
        $products = $query->with(['category'])->latest()->get();

        return response()->json([
            'message' => 'List produk berhasil diambil',
            'data' => $products
        ]);
    }

    /**
     * Ambil semua kategori
     */
    public function categories()
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'List kategori berhasil diambil',
            'data' => $categories
        ]);
    }

    /**
     * Ambil produk berdasarkan kategori
     */
    public function getByCategory($id)
    {
        $products = Product::where('category_id', $id)
            ->with(['category'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Produk berdasarkan kategori berhasil diambil',
            'data' => $products
        ]);
    }

    /**
     * Buat produk baru (Terproteksi)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Produk berhasil dibuat',
            'data' => $product
        ], 201);
    }

    /**
     * Update produk yang ada (Terproteksi)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Hapus produk (Terproteksi)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($product->image) Storage::disk('public')->delete($product->image);

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
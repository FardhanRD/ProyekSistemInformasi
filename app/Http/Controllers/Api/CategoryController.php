<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Ambil semua kategori dengan jumlah produk
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'message' => 'List kategori berhasil diambil',
            'data' => $categories
        ]);
    }

    /**
     * Ambil kategori beserta produk-produknya
     */
    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);

        return response()->json([
            'message' => 'Detail kategori berhasil diambil',
            'data' => $category
        ]);
    }

    /**
     * Buat kategori baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'message' => 'Kategori berhasil dibuat',
            'data' => $category
        ], 201);
    }

    /**
     * Update kategori yang ada (Khusus Admin)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category = Category::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'data' => $category->fresh()
        ]);
    }

    /**
     * Hapus kategori (Khusus Admin)
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}

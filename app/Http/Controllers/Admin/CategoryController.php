<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Query aman untuk kondisi tabel belum ada (terutama untuk environment test)
        if (!DB::getSchemaBuilder()->hasTable('kategori')) {
            $products = collect();
            $total_categories_active = 0;
        } else {
            $total_categories_active = Kategori::where('is_active', 1)->count();
        }

        if (!DB::getSchemaBuilder()->hasTable('produk')) {
            $products = collect();
        } else {
            $q = $request->get('q');
            $category_id = $request->get('category_id');

            $products = Produk::with(['kategori'])
                ->where('is_active', 1)
                ->when($q, fn($qq) => $qq->where('nama_produk', 'like', "%{$q}%"))
                ->when($category_id, fn($qq) => $qq->where('kategori_id', $category_id))
                ->orderBy('penyimpanan_waktu', 'desc')
                ->paginate(20)
                ->withQueryString();
        }

        $active_products = DB::getSchemaBuilder()->hasTable('produk')
            ? Produk::where('is_active', 1)->where('status_publish', 'publish')->count()
            : 0;

        // placeholder margin (sesuai requirement boleh kosong)
        $avg_margin = null;

        $top_level = [];
        if (DB::getSchemaBuilder()->hasTable('kategori')) {
            $top_level = Kategori::query()
                ->whereNull('parent_id')
                ->where('is_active', 1)
                ->orderBy('urutan')
                ->get();
        }

        // Basic distribution level 2 by product count
        $level2 = collect();
        if (DB::getSchemaBuilder()->hasTable('kategori') && DB::getSchemaBuilder()->hasTable('produk')) {
            $level2 = Kategori::query()
                ->where('is_active', 1)
                ->whereNotNull('parent_id')
                ->get()
                ->map(function ($c2) {
                    $productCount = Produk::where('is_active', 1)
                        ->where('kategori_id', $c2->kategori_id)
                        ->count();

                    return [
                        'parent_id' => $c2->parent_id,
                        'id' => $c2->kategori_id,
                        'nama' => $c2->nama_kategori,
                        'count' => $productCount,
                    ];
                })
                ->groupBy('parent_id');
        }

        $favorite_categories = collect();
        if (DB::getSchemaBuilder()->hasTable('kategori') && DB::getSchemaBuilder()->hasTable('produk')) {
            $favorite_categories = Kategori::query()
                ->where('is_active', 1)
                ->withCount(['produk as inventory_count' => function ($q) {
                    $q->where('is_active', 1);
                }])
                ->orderByDesc('inventory_count')
                ->limit(4)
                ->get();
        }

        return view('admin.category.index', [
            'total_categories_active' => $total_categories_active,
            'active_products' => $active_products,
            'avg_margin' => $avg_margin,
            'categories' => $products, // renamed: products now displayed in Master Category Explorer
            'top_level' => $top_level,
            'level2_grouped' => $level2,
            'favorite_categories' => $favorite_categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:kategori,kategori_id',
            'urutan' => 'nullable|integer|min:0',
            'banner_url' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
        ]);

        if (!DB::getSchemaBuilder()->hasTable('kategori')) {
            return back()->with('error', 'Tabel kategori belum tersedia.');
        }

        $slug = $validated['slug'] ?? Str::slug($validated['nama_kategori']);

        $level = null;
        if (!empty($validated['parent_id'])) {
            $parent = Kategori::find($validated['parent_id']);
            $level = $parent ? ($parent->level + 1) : 1;
        } else {
            $level = 1;
        }

        // Cegah duplikasi slug
        $slugBase = $slug;
        $suffix = 1;
        while (Kategori::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $suffix;
            $suffix++;
        }

        // banner_url tidak ada di model Kategori sekarang (fillable), jadi simpan hanya jika kolom ada
        $insert = [
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'level' => $level,
            'urutan' => $validated['urutan'] ?? 0,
            'is_active' => (int)$validated['is_active'],
        ];

        if (DB::getSchemaBuilder()->hasColumn('kategori', 'banner_url')) {
            $insert['banner_url'] = $validated['banner_url'] ?? null;
        }

        Kategori::create($insert);

        return redirect()->route('admin.category.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:kategori,kategori_id',
            'urutan' => 'nullable|integer|min:0',
            'banner_url' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['nama_kategori']);

        $level = null;
        if (!empty($validated['parent_id'])) {
            $parent = Kategori::find($validated['parent_id']);
            $level = $parent ? ($parent->level + 1) : 1;
        } else {
            $level = 1;
        }

        $update = [
            'nama_kategori' => $validated['nama_kategori'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'level' => $level,
            'urutan' => $validated['urutan'] ?? 0,
            'is_active' => (int)$validated['is_active'],
        ];

        if (DB::getSchemaBuilder()->hasColumn('kategori', 'banner_url')) {
            $update['banner_url'] = $validated['banner_url'] ?? null;
        }

        $kategori->update($update);

        return redirect()->route('admin.category.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!DB::getSchemaBuilder()->hasTable('kategori')) {
            return back()->with('error', 'Tabel kategori tidak tersedia.');
        }

        $kategori = Kategori::findOrFail($id);

        if (DB::getSchemaBuilder()->hasTable('produk')) {
            $hasProducts = Produk::where('kategori_id', $kategori->kategori_id)->count() > 0;
            if ($hasProducts) {
                return back()->with('error', 'Tidak bisa menghapus, masih ada produk terkait');
            }
        }

        // soft delete via is_active (karena requirement: error saat masih punya produk)
        $kategori->update(['is_active' => 0]);

        return redirect()->route('admin.category.index')->with('success', 'Kategori dihapus.');
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $startDate = $start ? date('Y-m-d', strtotime($start)) : null;
        $endDate = $end ? date('Y-m-d', strtotime($end)) : null;

        $events = [];

        // New categories
        if (Schema::hasTable('kategori')) {
            $newCats = Kategori::query()->where('is_active', 1);
            if ($startDate && $endDate) {
                $newCats->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }
            $newCats = $newCats->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get();

            foreach ($newCats as $row) {
                $events[] = [
                    'date' => $row->date,
                    'type' => 'new',
                    'count' => $row->count,
                ];
            }
        }

        return response()->json($events);
    }
}


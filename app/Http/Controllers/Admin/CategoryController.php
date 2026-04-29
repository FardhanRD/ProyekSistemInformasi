<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori
     */
    public function index(Request $request)
    {
        $selectedDate = $request->query('calendar_date', now()->toDateString());
        $selectedMonth = $request->query('calendar_month', now()->format('Y-m'));
        $selectedYear = (int) $request->query('calendar_year', now()->year);

        $calendarMonthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->setYear($selectedYear);

        $rootCategories = Category::query()
            ->whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->withCount('products')->orderBy('name');
                },
                'products',
            ])
            ->withCount(['products', 'children'])
            ->orderBy('name')
            ->get();

        $totalCategories = Category::count();
        $activeProducts = Product::count();

        $avgMargin = (float) OrderItem::query()
            ->whereNotNull('cost_price')
            ->where('price', '>', 0)
            ->selectRaw('COALESCE(AVG(((price - cost_price) / price) * 100), 0) as avg_margin')
            ->value('avg_margin');

        $hierarchyRows = $rootCategories->map(function (Category $category) {
            return [
                'category' => $category,
                'subcategories_count' => $category->children->count(),
            ];
        })->sortByDesc(function ($row) {
            return $row['subcategories_count'];
        })->take(3)->values();

        $favoriteCategories = $rootCategories->map(function (Category $category) {
            $coverProduct = $category->products->first();

            return [
                'category' => $category,
                'image' => $coverProduct?->image,
                'inventory' => (int) $category->products->sum('stock'),
                'status' => $category->products_count > 20 ? 'Top Sale' : ($category->products_count > 0 ? 'Stable' : 'New Arrival'),
            ];
        })->sortByDesc('inventory')->take(4)->values();

        $masterCategories = Category::query()
            ->whereNull('parent_id')
            ->withCount(['children', 'products'])
            ->with('children')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('movr.admin.categories.index', [
            'rootCategories' => $rootCategories,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'calendarMonthDate' => $calendarMonthDate,
            'totalCategories' => $totalCategories,
            'activeProducts' => $activeProducts,
            'avgMargin' => $avgMargin,
            'hierarchyRows' => $hierarchyRows,
            'favoriteCategories' => $favoriteCategories,
            'masterCategories' => $masterCategories,
        ]);
    }

    /**
     * Tampilkan form untuk membuat kategori baru
     */
    public function create()
    {
        return view('movr.admin.categories.create');
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dibuat!');
    }

    /**
     * Tampilkan form untuk edit kategori
     */
    public function edit(Category $kategori)
    {
        return view('movr.admin.categories.edit', compact('kategori'));
    }

    /**
     * Update kategori
     */
    public function update(Request $request, Category $kategori)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $kategori->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Hapus kategori
     */
    public function destroy(Category $kategori)
    {
        $kategori->delete();
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori dihapus!');
    }

    /**
     * Tampilkan detail kategori beserta produk
     */
    public function show(Request $request, Category $kategori)
    {
        $search = trim((string) $request->query('search', ''));
        $backUrl = $request->query('back', route('admin.kategori.index'));

        $products = Product::query()
            ->with(['category', 'supplier'])
            ->where('category_id', $kategori->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery->where('store_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('movr.admin.categories.show', compact('kategori', 'products', 'search', 'backUrl'));
    }
}
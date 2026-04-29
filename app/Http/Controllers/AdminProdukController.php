<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category; 
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProdukController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $calendarDate = $request->query('calendar_date', now()->toDateString());
        $calendarMonth = $request->query('calendar_month', now()->format('Y-m'));
        $calendarYear = (int) $request->query('calendar_year', now()->year);
        $calendarMonthDate = Carbon::createFromFormat('Y-m', $calendarMonth)->setYear($calendarYear);

        $products = Product::with(['category', 'supplier'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $lowStockCount = Product::where('stock', '<=', 10)->count();
        $inventoryValue = (float) Product::selectRaw('COALESCE(SUM(price * stock), 0) as total')->value('total');

        $popularProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sales'))
            ->whereNotNull('product_id')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get()
            ->filter(fn ($item) => $item->product !== null)
            ->values();

        if ($popularProducts->isEmpty()) {
            $popularProducts = Product::latest()
                ->limit(5)
                ->get()
                ->map(function ($product) {
                    return (object) [
                        'product_id' => $product->id,
                        'product' => $product,
                        'total_sales' => max(1, (int) round($product->stock * 0.5)),
                    ];
                });
        }

        $femaleCount = Product::whereHas('category', function ($query) {
            $query->where('name', 'like', '%women%')
                ->orWhere('name', 'like', '%woman%')
                ->orWhere('name', 'like', '%cewe%')
                ->orWhere('name', 'like', '%wanita%');
        })->count();

        $maleCount = Product::whereHas('category', function ($query) {
            $query->where('name', 'like', '%men%')
                ->orWhere('name', 'like', '%man%')
                ->orWhere('name', 'like', '%cowo%')
                ->orWhere('name', 'like', '%pria%');
        })->count();

        if (($femaleCount + $maleCount) === 0) {
            $femaleCount = 65;
            $maleCount = 35;
        }

        $genderTotal = max(1, $femaleCount + $maleCount);
        $genderDistribution = [
            'women' => (int) round(($femaleCount / $genderTotal) * 100),
            'men' => (int) round(($maleCount / $genderTotal) * 100),
        ];

        $stats = [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'lowStockCount' => $lowStockCount,
            'inventoryValue' => $inventoryValue,
            'popularProducts' => $popularProducts,
            'genderDistribution' => $genderDistribution,
        ];

        return view('movr.admin.produk.index', compact('products', 'search', 'stats', 'calendarDate', 'calendarMonth', 'calendarYear', 'calendarMonthDate'));
    }

    public function storeVariants(Request $request, $id)
    {
        $request->validate([
            'variants' => 'required|array',
            'variants.*.variant_name' => 'required|string|max:255',
            'variants.*.initial_stock' => 'nullable|integer|min:0',
            'variants.*.color' => 'nullable|string',
            'variants.*.size' => 'nullable|string',
            'variants.*.min_stock' => 'nullable|integer|min:0',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.images' => 'nullable|array',
            'variants.*.images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);
        
        foreach ($request->input('variants', []) as $variantData) {
            if (empty($variantData['variant_name'])) {
                continue;
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => $variantData['variant_name'],
                'color' => $variantData['color'] ?? null,
                'size' => $variantData['size'] ?? null,
                'initial_stock' => $variantData['initial_stock'] ?? 0,
                'min_stock' => $variantData['min_stock'] ?? 10,
                'price_adjustment' => $variantData['price_adjustment'] ?? null,
                'is_active' => true,
            ]);

            // Handle variant images
            if (!empty($variantData['images'])) {
                foreach ($variantData['images'] as $image) {
                    $path = $image->store('variants', 'public');
                    // TODO: Create variant image record in database
                }
            }
        }

        return redirect()->route('admin.produk.index')->with('success', 'Variants created successfully!');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'supplier', 'images'])->findOrFail($id);

        return view('movr.admin.produk.show', compact('product'));
    }

    public function create()
    {
        
        $categories = Category::all();
        $suppliers = Supplier::orderBy('store_name')->get();
        return view('movr.admin.produk.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'material_build' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,unisex',
            'sport_type' => 'nullable|string|max:100',
            'min_stock_alert' => 'nullable|integer|min:0',
            'visibility' => 'nullable|in:public,private,hidden',
            'tags' => 'nullable|string|max:255',
            '_action' => 'nullable|in:save,draft,publish',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedFile) {
                $uploadedImages[] = $uploadedFile->store('products', 'public');
            }
            $imagePath = $uploadedImages[0] ?? null;
        }

        $tags = array_values(array_filter(array_map(
            static fn ($tag) => trim((string) $tag),
            explode(',', (string) $request->input('tags', ''))
        )));

        $action = $request->input('_action', 'publish');
        $visibility = $request->input('visibility', 'public');

        $metadata = array_filter([
            'material_build' => $request->input('material_build'),
            'gender' => $request->input('gender'),
            'sport_type' => $request->input('sport_type'),
            'min_stock_alert' => $request->filled('min_stock_alert') ? (int) $request->input('min_stock_alert') : null,
            'visibility' => $visibility,
            'tags' => $tags ?: null,
            'action' => $action,
        ], static fn ($value) => $value !== null && $value !== '');

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id ?? null,
            'supplier_id' => $request->supplier_id ?? null,
            'image' => $imagePath,
            'metadata' => $metadata ?: null,
            'user_id' => auth()->id(), 
        ]);

        foreach ($uploadedImages as $path) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }

        $messages = [
            'save' => 'Produk berhasil disimpan!',
            'draft' => 'Produk disimpan sebagai draft!',
            'publish' => 'Produk berhasil dipublikasikan!'
        ];
        
        return redirect()->route('admin.produk.variants', $product->id)->with('success', $messages[$action] ?? 'Produk berhasil disimpan!');
    }

    public function variants($id)
    {
        $product = Product::findOrFail($id);
        return view('movr.admin.produk.variants', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        $suppliers = Supplier::orderBy('store_name')->get();
        return view('movr.admin.produk.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'material_build' => 'nullable|string|max:255',
            'gender' => 'nullable|in:men,women,unisex',
            'sport_type' => 'nullable|string|max:100',
            'min_stock_alert' => 'nullable|integer|min:0',
            'visibility' => 'nullable|in:draft,published,scheduled',
            'tags' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::with('images')->findOrFail($id);
        
        $data = $request->except(['images', '_token', '_method']);

        $tags = array_values(array_filter(array_map(
            static fn ($tag) => trim((string) $tag),
            explode(',', (string) $request->input('tags', ''))
        )));

        $data['metadata'] = array_filter([
            'material_build' => $request->input('material_build'),
            'gender' => $request->input('gender'),
            'sport_type' => $request->input('sport_type'),
            'min_stock_alert' => $request->filled('min_stock_alert') ? (int) $request->input('min_stock_alert') : null,
            'visibility' => $request->input('visibility', data_get($product->metadata, 'visibility', 'published')),
            'tags' => $tags ?: null,
        ], static fn ($value) => $value !== null && $value !== '');

        if ($request->hasFile('images')) {
            $newImagePaths = [];
            foreach ($request->file('images') as $uploadedFile) {
                $newImagePaths[] = $uploadedFile->store('products', 'public');
            }

            if ($newImagePaths) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $product->images()->delete();

                foreach ($newImagePaths as $path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }

                if ($product->image && !in_array($product->image, $newImagePaths, true)) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $newImagePaths[0];
            }
        }

        $product->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();
        
       
        return back()->with('success', 'Produk berhasil dihapus!');
    }
}
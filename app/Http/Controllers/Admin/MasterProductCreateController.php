<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\GambarProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\ProdukSupplier;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\WarnaProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterProductCreateController extends Controller
{
    public function create()
    {
        $kategoris = Kategori::with('children.children')
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->orderBy('urutan')
            ->get();

        $suppliers = Supplier::where('is_verified', 1)
            ->orderBy('nama_toko')
            ->get();

        $sport_types = [
            'Running',
            'Futsal',
            'Gym',
            'Lifestyle',
            'Training',
            'Cycling',
            'Outdoor',
            'Casual',
            'Basketball',
            'Swimming'
        ];

        return view('admin.master-product.create', compact('kategoris', 'suppliers', 'sport_types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'supplier_id' => 'required|exists:supplier,supplier_id',
            'spesifikasi' => 'nullable|string',
            'gender' => 'required|in:men,women,unisex,kids',
            'tipe_olahraga' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'status_publish' => 'required|in:publish,draft,scheduled',
            'scheduled_at' => 'required_if:status_publish,scheduled|nullable|date',
            'is_featured' => 'nullable|boolean',
        ]);

        // Generate slug unik
        $slugBase = Str::slug($validated['nama_produk']);
        $slugCount = Produk::where('slug', 'like', $slugBase . '%')->count();
        $validated['slug'] = $slugCount ? $slugBase . '-' . $slugCount : $slugBase;

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['stok_minimum'] = $validated['stok_minimum'] ?? 5;

        session(['product_step1' => $validated]);

        return redirect()->route('admin.master-product.variant.create');
    }

    // STEP 2
    public function createVariant()
    {
        if (!session('product_step1')) {
            return redirect()->route('admin.master-product.create')
                ->with('error', 'Silakan isi informasi produk dulu');
        }

        $step1 = session('product_step1');

        $ukurans = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '38', '39', '40', '41', '42', '43'];

        $daftarWarna = [
            'Hitam' => '#000000',
            'Putih' => '#FFFFFF',
            'Merah' => '#FF0000',
            'Biru' => '#0000FF',
            'Hijau' => '#008000',
            'Kuning' => '#FFFF00',
            'Abu-abu' => '#808080',
            'Coklat' => '#A52A2A',
            'Navy' => '#000080',
            'Pink' => '#FFC0CB',
            'Ungu' => '#800080',
            'Orange' => '#FFA500',
        ];

        return view('admin.master-product.create-variant', compact('step1', 'ukurans', 'daftarWarna'));
    }

    public function storeVariant(Request $request)
    {
        if (!session('product_step1')) {
            return redirect()->route('admin.master-product.create')
                ->with('error', 'Session habis, mulai ulang');
        }

        $request->validate([
            'variants' => 'required|array|min:1',
            'variants.*.nama_variant' => 'required|string',
            'variants.*.ukuran' => 'required|string',
            'variants.*.nama_warna' => 'required|string',
            'variants.*.kode_hex' => 'required|string',
            'variants.*.stok_awal' => 'required|integer|min:0',
            'variants.*.stok_minimum' => 'required|integer|min:0',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.is_active' => 'required|in:0,1',
        ]);

        session(['product_step2' => $request->variants]);

        return redirect()->route('admin.master-product.media.create');
    }

    // STEP 3
    public function createMedia()
    {
        if (!session('product_step1') || !session('product_step2')) {
            return redirect()->route('admin.master-product.create')
                ->with('error', 'Session habis, mulai ulang');
        }

        $step1 = session('product_step1');
        $step2 = session('product_step2');

        return view('admin.master-product.create-media', compact('step1', 'step2'));
    }

    public function storeMedia(Request $request)
    {
        \Log::info('=== STORE MEDIA DIPANGGIL ===');
        \Log::info('Step1:', session('product_step1') ?? []);
        \Log::info('Step2:', session('product_step2') ?? []);

        if (!session('product_step1') || !session('product_step2')) {
            return redirect()->route('admin.master-product.create')
                ->with('error', 'Session habis, mulai ulang');
        }

        $request->validate([
            'gambar.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif|max:10240',
            // file variant sesuai blade: variant_gambar_{idx}[]
        ]);

        $step1 = session('product_step1');
        $variants = session('product_step2');


        DB::beginTransaction();

        try {
            $produk = Produk::create([
                'supplier_id'    => $step1['supplier_id'],
                'kategori_id'    => $step1['kategori_id'],
                'nama_produk'    => $step1['nama_produk'],
                'slug'           => $step1['slug'],
                'deskripsi'      => $step1['deskripsi'] ?? '',
                'spesifikasi'    => $step1['spesifikasi'] ?? null,
                'gender'         => $step1['gender'],
                'tipe_olahraga'  => $step1['tipe_olahraga'] ?? null,
                'harga_dasar'    => $step1['harga_dasar'] ?? 0,
                'stok_minimum'   => $step1['stok_minimum'] ?? 5,
                'status_publish' => $step1['status_publish'] ?? 'draft',
                'scheduled_at'   => $step1['scheduled_at'] ?? null,
                'tags'            => $step1['tags'] ?? null,
                'is_featured'    => $step1['is_featured'] ?? false,
                'is_active'      => 1,
            ]);


            // 1) Insert gambar utama (urutan 0..n)
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $idx => $file) {
                    $path = $file->store('products', 'public');
                    GambarProduk::create([
                        'produk_id' => $produk->produk_id,
                        'url_gambar' => $path,
                        'alt_text' => $produk->nama_produk,
                        'urutan' => (int) $idx,
                    ]);
                }
            }

            // 2) Variants + warna + detail_produk + stock_movement + gambar variant
            foreach ($variants as $idx => $variant) {
                // Simpan master warna global (schema warna_produk tidak punya produk_id)
                WarnaProduk::firstOrCreate(
                    [
                        'nama_warna' => $variant['nama_warna'],
                    ],
                    [
                        'kode_hex' => $variant['kode_hex'],
                    ]
                );

                // SKU unik
                $skuBase = 'SKU-' . str_pad((string) $produk->produk_id, 3, '0', STR_PAD_LEFT)
                    . '-' . strtoupper((string) $variant['ukuran'])
                    . '-' . Str::slug((string) $variant['nama_warna']);

                $sku = $skuBase;
                $skuCount = 0;
                while (DetailProduk::where('sku', $sku)->exists()) {
                    $skuCount++;
                    $sku = $skuBase . '-' . $skuCount;
                }

                $harga = floatval($step1['harga_dasar']) + floatval($variant['price_adjustment'] ?? 0);

                $detail = DetailProduk::create([
                    'produk_id' => $produk->produk_id,
                    'nama_produk' => $variant['nama_variant'],
                    'ukuran' => $variant['ukuran'],
                    'harga' => $harga,
                    'stok' => intval($variant['stok_awal']),
                    'sku' => $sku,
                    'berat_gram' => 0,
                    'is_active' => intval($variant['is_active']),
                ]);

                StockMovement::create([
                    'detail_produk_id' => $detail->detail_produk_id,
                    'jenis' => 'in',
                    'qty' => intval($variant['stok_awal']),
                    'stok_sebelum' => 0,
                    'stok_sesudah' => intval($variant['stok_awal']),
                    'referensi' => 'initial_stock',
                    'catatan' => 'Stok awal saat produk dibuat',
                    'dibuat_oleh' => auth()->user()->pengguna_id,
                ]);

                // Gambar variant: input name harus variant_gambar_{i}[] sesuai blade
                $keyGambar = 'variant_gambar_' . $idx;
                if ($request->hasFile($keyGambar)) {
                    foreach ($request->file($keyGambar) as $gi => $file) {
                        $path = $file->store('products/variants', 'public');
                        GambarProduk::create([
                            'produk_id' => $produk->produk_id,
                            'url_gambar' => $path,
                            'alt_text' => $variant['nama_variant'],
                            'urutan' => 10 + ($idx * 5) + (int) $gi,
                        ]);
                    }
                }
            }

            // Update harga_dasar = MIN harga variant
            $minHarga = DetailProduk::where('produk_id', $produk->produk_id)
                ->min('harga');

            $produk->update(['harga_dasar' => $minHarga]);

            // Produk supplier (produk_supplier)
            ProdukSupplier::create([
                'supplier_id' => $step1['supplier_id'],
                'produk_id' => $produk->produk_id,
                'harga_modal' => 0,
                'catatan' => null,
            ]);


            session()->forget(['product_step1', 'product_step2']);

            DB::commit();

            return redirect()->route('admin.master-product.index')
                ->with('success', 'Produk "' . $produk->nama_produk . '" berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('GAGAL SIMPAN: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }

    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['is_active' => 0]);

        return redirect()->route('admin.master-product.index')
            ->with('success', 'Produk berhasil dinonaktifkan');
    }
}


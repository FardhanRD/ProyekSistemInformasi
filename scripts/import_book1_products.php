<?php

use App\Models\Kategori;
use App\Models\DetailProduk;
use App\Models\GambarProduk;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\WarnaProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = [
    [
        'source_no' => 1,
        'url' => 'https://www.laicaactive.com/cdn/shop/files/028A1983.jpg?v=1778128064&width=600',
        'nama_produk' => 'LAICA TUMBHOLE JACKET',
        'category' => 'WOMEN',
        'sub_category' => 'Jacket',
        'deskripsi' => "Embrace comfort and control with the Laica Thumbhole Jacket — Designed with thumbholes to keep your sleeves in place, this jacket offers added warmth and coverage during your workouts. The soft, stretchy fabric moves with you, providing a snug fit that enhances your performance.",
        'supplier' => 'LAICA',
        'material_build' => '-',
        'gender' => 'Women',
        'base_price' => '459.900',
        'visibility' => 'public',
        'warna' => 'Ivory',
        'ukuran' => 'M',
        'gambar_varian' => 'https://www.laicaactive.com/cdn/shop/files/028A89081_68502e29-8220-4ba2-b066-2b39c995b2d8.jpg?v=1778128498&width=600',
        'gambar_1' => 'https://www.laicaactive.com/cdn/shop/files/028A1983.jpg?v=1778128064&width=700',
        'gambar_2' => 'https://www.laicaactive.com/cdn/shop/files/ivory_3.png?v=1778128282&width=700',
        'gambar_3' => 'https://www.laicaactive.com/cdn/shop/files/028A1987.jpg?v=1778128064&width=600',
    ],
];

DB::transaction(function () use ($rows) {
    $supplier = Supplier::whereRaw('LOWER(nama_toko) = ?', [Str::lower('LAICA')])->first();
    if (! $supplier) {
        throw new RuntimeException('Supplier LAICA tidak ditemukan.');
    }

    $womenCategory = Kategori::whereRaw('LOWER(nama_kategori) = ?', [Str::lower('WOMEN')])->first();
    if (! $womenCategory) {
        throw new RuntimeException('Kategori WOMEN tidak ditemukan.');
    }

    $jacketCategory = Kategori::firstOrCreate(
        [
            'slug' => Str::slug('WOMEN Jacket'),
        ],
        [
            'nama_kategori' => 'Jacket',
            'parent_id' => $womenCategory->kategori_id,
            'level' => 2,
            'urutan' => (int) Kategori::where('parent_id', $womenCategory->kategori_id)->max('urutan') + 1,
            'is_active' => 1,
        ]
    );

    $warna = WarnaProduk::firstOrCreate(
        ['nama_warna' => 'Ivory'],
        ['kode_hex' => '#FFFFF0', 'is_active' => 1]
    );

    foreach ($rows as $row) {
        $price = (float) str_replace('.', '', $row['base_price']);
        $slug = Str::slug($row['nama_produk']);
        $images = array_values(array_unique(array_filter([
            $row['url'] ?? null,
            $row['gambar_varian'] ?? null,
            $row['gambar_1'] ?? null,
            $row['gambar_2'] ?? null,
            $row['gambar_3'] ?? null,
        ])));

        $product = Produk::where('slug', $slug)->first();
        if ($product) {
            GambarProduk::where('produk_id', $product->produk_id)->delete();
            $product->update([
                'supplier_id' => $supplier->supplier_id,
                'kategori_id' => $jacketCategory->kategori_id,
                'nama_produk' => $row['nama_produk'],
                'deskripsi' => $row['deskripsi'],
                'spesifikasi' => $row['material_build'],
                'gender' => Str::lower($row['gender']),
                'tipe_olahraga' => null,
                'harga_dasar' => $price,
                'stok_minimum' => 5,
                'status_publish' => 'publish',
                'scheduled_at' => null,
                'tags' => [$row['category'], $row['sub_category'], $row['gender'], $row['warna']],
                'is_featured' => 0,
                'is_active' => 1,
            ]);
        } else {
            $product = Produk::create([
                'supplier_id' => $supplier->supplier_id,
                'kategori_id' => $jacketCategory->kategori_id,
                'nama_produk' => $row['nama_produk'],
                'slug' => $slug,
                'deskripsi' => $row['deskripsi'],
                'spesifikasi' => $row['material_build'],
                'gender' => Str::lower($row['gender']),
                'tipe_olahraga' => null,
                'harga_dasar' => $price,
                'stok_minimum' => 5,
                'status_publish' => 'publish',
                'scheduled_at' => null,
                'tags' => [$row['category'], $row['sub_category'], $row['gender'], $row['warna']],
                'is_featured' => 0,
                'is_active' => 1,
            ]);
        }

        DetailProduk::updateOrCreate(
            [
                'produk_id' => $product->produk_id,
                'warna_id' => $warna->warna_id,
                'ukuran' => $row['ukuran'],
            ],
            [
                'nama_produk' => $product->nama_produk,
                'harga' => $price,
                'stok' => 0,
                'sku' => strtoupper(Str::slug($product->nama_produk . '-' . $row['warna'] . '-' . $row['ukuran'])),
                'berat_gram' => 0,
                'is_active' => 1,
            ]
        );

        foreach ($images as $index => $imageUrl) {
            GambarProduk::create([
                'produk_id' => $product->produk_id,
                'url_gambar' => $imageUrl,
                'alt_text' => $product->nama_produk,
                'urutan' => $index,
            ]);
        }

        echo "Imported: {$product->nama_produk} (ID: {$product->produk_id})\n";
    }
});

echo "Done\n";

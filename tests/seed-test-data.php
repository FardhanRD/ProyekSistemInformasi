<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\DetailProduk;
use App\Models\GambarProduk;
use App\Models\WarnaProduk;

// Create or get category
$category = Kategori::firstOrCreate(['slug' => 'sepatu'], [
    'nama_kategori' => 'Sepatu',
    'level' => 1,
    'is_active' => 1
]);

// Create product
$product = Produk::firstOrCreate(
    ['slug' => 'sepatu-olahraga-premium'],
    [
        'supplier_id' => 1,
        'kategori_id' => $category->kategori_id,
        'nama_produk' => 'Sepatu Olahraga Premium',
        'deskripsi' => 'Sepatu olahraga berkualitas tinggi dengan desain modern dan kenyamanan maksimal untuk aktivitas sehari-hari maupun olahraga intensif. Diproduksi dengan material terbaik untuk daya tahan maksimal.',
        'harga_dasar' => 299000,
        'spesifikasi_material' => 'Leather & Mesh',
        'spesifikasi_gender' => 'Unisex',
        'spesifikasi_tipe' => 'Running Shoe',
        'is_active' => 1
    ]
);

// Create color variants (as global colors)
$colors = [
    ['nama' => 'Hitam', 'hex' => '#000000'],
    ['nama' => 'Putih', 'hex' => '#FFFFFF'],
    ['nama' => 'Merah', 'hex' => '#FF0000'],
];

$createdColors = [];
foreach ($colors as $color) {
    $w = WarnaProduk::firstOrCreate(
        ['nama_warna' => $color['nama']],
        ['kode_hex' => $color['hex'], 'is_active' => 1]
    );
    $createdColors[$color['nama']] = $w;
}

// Create size variants
$sizes = ['38', '39', '40', '41', '42', '43', '44'];

echo "Creating product variants...\n";
foreach ($sizes as $size) {
    DetailProduk::create([
        'produk_id' => $product->produk_id,
        'nama_produk' => "Sepatu Olahraga Premium - Size $size",
        'ukuran' => $size,
        'harga' => 299000,
        'stok' => 50,
        'sku' => "SKU-PROD-{$product->produk_id}-$size",
        'berat_gram' => 350,
        'is_active' => 1
    ]);
}

echo "✓ Test data created successfully!\n";
echo "Product ID: {$product->produk_id}\n";
echo "Slug: {$product->slug}\n";
echo "Access at: http://127.0.0.1:8000/produk/{$product->slug}\n";

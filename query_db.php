<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== detailProducts ===\n";
$detailProducts = App\Models\DetailProduk::with(['produk', 'warna'])->get();
foreach ($detailProducts as $detail) {
    $label = ($detail->produk?->nama_produk ?? '-') 
        . ($detail->warna?->nama_warna ? ' - ' . $detail->warna->nama_warna : '')
        . ($detail->ukuran ? ' (Size: ' . $detail->ukuran . ')' : '');
    echo "ID: {$detail->detail_produk_id}, Label: '{$label}'\n";
}










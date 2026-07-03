<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$detailProducts = DB::table('detail_produk')
    ->join('produk', 'detail_produk.produk_id', '=', 'produk.produk_id')
    ->leftJoin('warna_produk', 'detail_produk.warna_id', '=', 'warna_produk.warna_id')
    ->select('detail_produk.detail_produk_id', 'produk.nama_produk', 'warna_produk.nama_warna', 'detail_produk.ukuran')
    ->get();

echo "=== Varian Products in dropdown ===\n";
foreach ($detailProducts as $detail) {
    $label = $detail->nama_produk;
    if ($detail->nama_warna) {
        $label .= " - " . $detail->nama_warna;
    }
    if ($detail->ukuran) {
        $label .= " (Size: " . $detail->ukuran . ")";
    }
    echo "Value: {$detail->detail_produk_id}, Label: '{$label}'\n";
}

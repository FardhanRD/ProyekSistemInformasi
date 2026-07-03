<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produk;
use Illuminate\Support\Facades\DB;

use App\Models\GambarProduk;
use App\Models\GambarDetailProduk;

// Find a valid existing image file to use as a template
$existingFiles = glob(storage_path('app/public/products/*.webp'));
if (empty($existingFiles)) {
    echo "Error: No webp files found in public/products storage directory to use as a template.\n";
    exit(1);
}
$templateFile = $existingFiles[0];
echo "Using template image: " . basename($templateFile) . "\n";

$restoredCount = 0;

// 1. Restore GambarProduk
echo "Checking GambarProduk...\n";
foreach (GambarProduk::all() as $img) {
    $url = $img->url_gambar;
    if (!empty($url) && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
        $targetPath = storage_path('app/public/' . ltrim($url, '/'));
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($targetPath)) {
            if (copy($templateFile, $targetPath)) {
                $restoredCount++;
                echo "Restored GambarProduk (ID: {$img->gambar_id}): " . basename($targetPath) . "\n";
            } else {
                echo "Failed to copy template to " . $targetPath . "\n";
            }
        }
    }
}

// 2. Restore GambarDetailProduk
echo "Checking GambarDetailProduk...\n";
foreach (GambarDetailProduk::all() as $img) {
    $url = $img->url_gambar;
    if (!empty($url) && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
        $targetPath = storage_path('app/public/' . ltrim($url, '/'));
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($targetPath)) {
            if (copy($templateFile, $targetPath)) {
                $restoredCount++;
                echo "Restored GambarDetailProduk (ID: {$img->gambar_detail_id}): " . basename($targetPath) . "\n";
            } else {
                echo "Failed to copy template to " . $targetPath . "\n";
            }
        }
    }
}

echo "\nDone! Restored $restoredCount missing product/detail images.\n";

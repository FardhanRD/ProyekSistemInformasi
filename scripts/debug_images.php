<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produk;

$products = Produk::where('is_active', 1)->with('gambarUtama')->get();

echo "Total active products: " . $products->count() . "\n";
$missingCount = 0;
$localCount = 0;
$externalCount = 0;

foreach ($products as $p) {
    if ($p->gambarUtama) {
        $url = $p->gambarUtama->url_gambar;
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $externalCount++;
        } else {
            $localCount++;
            $filePath = storage_path('app/public/' . ltrim($url, '/'));
            if (!file_exists($filePath)) {
                $missingCount++;
                echo "MISSING FILE: Product '{$p->nama_produk}' (ID: {$p->produk_id}) expected '{$url}' but file does not exist at '{$filePath}'\n";
            }
        }
    } else {
        echo "NO IMAGE: Product '{$p->nama_produk}' has no image.\n";
    }
}

echo "\nSummary:\n";
echo "Local images: $localCount ($missingCount missing)\n";
echo "External images: $externalCount\n";

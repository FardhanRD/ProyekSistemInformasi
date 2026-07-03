<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\RatingProduk;

$r = RatingProduk::find(4);
if ($r) {
    $r->update([
        'balasan' => null,
        'balas_oleh' => null,
        'balas_tanggal' => null,
    ]);
    echo "Reset review ID 4 successfully.\n";
} else {
    RatingProduk::create([
        'rating_id' => 4,
        'produk_id' => 20,
        'buyer_id' => 1,
        'transaksi_id' => 1,
        'bintang' => 5,
        'judul_ulasan' => 'Barang Bagus',
        'isi_ulasan' => 'Barangnya bagus banget, pas di kepala.',
        'is_verified' => true,
        'created_at' => now(),
    ]);
    echo "Created new unreplied review with ID 4.\n";
}

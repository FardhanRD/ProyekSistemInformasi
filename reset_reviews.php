<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\RatingProduk;

// Reset/buat Review Pertama (transaksi_id => 1) untuk dibalas di TC-MKT-04
RatingProduk::updateOrCreate(
    ['produk_id' => 20, 'buyer_id' => 1, 'transaksi_id' => 1],
    [
        'bintang' => 5,
        'judul_ulasan' => 'Barang Bagus',
        'isi_ulasan' => 'Barangnya bagus banget, pas di kepala.',
        'is_verified' => true,
        'balasan' => null,
        'balas_oleh' => null,
        'balas_tanggal' => null,
        'created_at' => now(),
    ]
);

// Reset/buat Review Kedua (transaksi_id => 2) untuk dihapus di TC-MKT-05
RatingProduk::updateOrCreate(
    ['produk_id' => 20, 'buyer_id' => 1, 'transaksi_id' => 2],
    [
        'bintang' => 2,
        'judul_ulasan' => 'Ulasan Kasar',
        'isi_ulasan' => 'Kualitas produk sangat buruk dan pelayanan jelek sekali.',
        'is_verified' => true,
        'balasan' => null,
        'balas_oleh' => null,
        'balas_tanggal' => null,
        'created_at' => now()->subDay(), // Diatur lebih lama agar berada di baris kedua (Urutan Descending)
    ]
);

echo "Database successfully reset with 2 reviews.\n";

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Menjalankan ALTER TABLE rating_toko...\n";
    
    // Check if kategori column exists
    $columns = DB::select('SHOW COLUMNS FROM rating_toko');
    $columnNames = array_map(fn($col) => $col->Field, $columns);
    
    if (!in_array('kategori', $columnNames)) {
        DB::statement("ALTER TABLE rating_toko ADD COLUMN kategori ENUM('pelayanan','aplikasi') NOT NULL DEFAULT 'pelayanan'");
        echo "✓ Kolom kategori ditambahkan\n";
    } else {
        echo "• Kolom kategori sudah ada\n";
    }
    
    if (!in_array('transaksi_id', $columnNames)) {
        DB::statement("ALTER TABLE rating_toko ADD COLUMN transaksi_id INT UNSIGNED NULL AFTER buyer_id");
        echo "✓ Kolom transaksi_id ditambahkan\n";
    } else {
        echo "• Kolom transaksi_id sudah ada\n";
    }
    
    echo "\n✓ Database schema berhasil diupdate!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

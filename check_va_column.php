<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Check table structure
    $columns = Schema::getColumns('pembayaran');
    echo "Columns in 'pembayaran' table:\n";
    foreach ($columns as $col) {
        echo "- " . $col['name'] . " (" . $col['type'] . ")\n";
    }
    
    echo "\n\nAdding nomor_va column if not exists...\n";
    $hasColumn = Schema::hasColumn('pembayaran', 'nomor_va');
    
    if (!$hasColumn) {
        // Add at the end instead of after kode_unik
        DB::statement('ALTER TABLE pembayaran ADD COLUMN nomor_va VARCHAR(30) NULL COMMENT "Nomor Virtual Account"');
        echo "✓ Column 'nomor_va' added successfully!\n";
    } else {
        echo "✓ Column 'nomor_va' already exists\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Suppliers in dropdown ===\n";
foreach (DB::table('supplier')->select('supplier_id', 'nama_toko', 'nama_owner')->get() as $s) {
    echo "Value: {$s->supplier_id}, Label: '{$s->nama_toko} (Owner: {$s->nama_owner})'\n";
}

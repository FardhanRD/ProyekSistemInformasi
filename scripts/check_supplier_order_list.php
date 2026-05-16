<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SupplierOrder;

$orders = SupplierOrder::with(['supplier','admin.pengguna'])->orderBy('tanggal_order','desc')->get();

echo "Found orders: " . $orders->count() . "\n";
foreach ($orders as $o) {
    echo "ID: {$o->supplier_order_id}, KODE: {$o->kode_order}, SUPPLIER_ID: {$o->supplier_id}, STATUS: {$o->status}, TOTAL_HARGA: {$o->total_harga}\n";
}

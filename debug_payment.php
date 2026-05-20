<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking metode_pembayaran table:\n";
    $metodes = DB::table('metode_pembayaran')->select('metode_id', 'metode', 'jenis', 'is_active')->get();
    
    foreach ($metodes as $m) {
        echo "ID: {$m->metode_id} | Metode: {$m->metode} | Jenis: {$m->jenis} | Active: {$m->is_active}\n";
    }
    
    echo "\n\nChecking pembayaran with latest transaction:\n";
    $pembayaran = DB::table('pembayaran')
        ->latest('pembayaran_id')
        ->first();
    
    if ($pembayaran) {
        echo "Pembayaran ID: {$pembayaran->pembayaran_id}\n";
        echo "Transaksi ID: {$pembayaran->transaksi_id}\n";
        echo "Metode ID: {$pembayaran->metode_id}\n";
        echo "Nomor VA: {$pembayaran->nomor_va}\n";
        echo "Status: {$pembayaran->status_pembayaran}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

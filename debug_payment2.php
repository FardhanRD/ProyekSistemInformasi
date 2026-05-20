<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

try {
    echo "Checking latest pembayaran with metode info:\n";
    
    $pembayaran = Pembayaran::with('metode')
        ->latest()
        ->first();
    
    if ($pembayaran) {
        echo "Pembayaran ID: {$pembayaran->pembayaran_id}\n";
        echo "Transaksi ID: {$pembayaran->transaksi_id}\n";
        echo "Metode ID: {$pembayaran->metode_id}\n";
        echo "Metode Name: " . ($pembayaran->metode?->metode ?? 'NULL') . "\n";
        echo "Metode Jenis: " . ($pembayaran->metode?->jenis ?? 'NULL') . "\n";
        echo "Nomor VA: '" . ($pembayaran->nomor_va ?? '') . "'\n";
        
        $jenis = (string) ($pembayaran->metode->jenis ?? '');
        $contains = str_contains(strtolower($jenis), 'transfer');
        echo "Jenis contains 'transfer': " . ($contains ? 'YES' : 'NO') . "\n";
        
        $transaksi = Transaksi::with('buyer')->find($pembayaran->transaksi_id);
        if ($transaksi) {
            echo "Transaksi Buyer ID: {$transaksi->buyer->pengguna_id}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

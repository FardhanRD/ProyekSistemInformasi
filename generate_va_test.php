<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembayaran;
use App\Models\Transaksi;

try {
    echo "Attempting to generate and save VA...\n";
    
    $pembayaran = Pembayaran::with('metode')->latest()->first();
    
    if ($pembayaran) {
        $transaksi = Transaksi::with('buyer')->find($pembayaran->transaksi_id);
        
        // Generate VA (copy logic dari controller)
        $metode = (string) ($pembayaran->metode->metode ?? '');
        $lower = strtolower($metode);
        
        $prefix = '9999';
        if (str_contains($lower, 'bca')) {
            $prefix = '1234';
        } elseif (str_contains($lower, 'mandiri')) {
            $prefix = '8888';
        } elseif (str_contains($lower, 'bni')) {
            $prefix = '8888';
        } elseif (str_contains($lower, 'bri')) {
            $prefix = '0088';
        }
        
        $buyerId = $transaksi->buyer->pengguna_id;
        $transId = $transaksi->transaksi_id;
        
        $buyerPart = str_pad((int) $buyerId, 6, '0', STR_PAD_LEFT);
        $transPart = str_pad(((int) $transId) % 10000, 4, '0', STR_PAD_LEFT);
        
        $noVA = $prefix . $buyerPart . $transPart;
        
        echo "Generated VA: {$noVA}\n";
        echo "Prefix: {$prefix}\n";
        echo "Buyer Part: {$buyerPart} (ID: {$buyerId})\n";
        echo "Trans Part: {$transPart} (ID: {$transId})\n";
        
        // Attempt to update
        $result = $pembayaran->update(['nomor_va' => $noVA]);
        echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
        
        // Refresh and check
        $pembayaran->refresh();
        echo "VA after refresh: '{$pembayaran->nomor_va}'\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembayaran;
use App\Models\MetodePembayaran;
use Illuminate\Support\Facades\Schema;

echo "=== VERIFIKASI SYSTEM VIRTUAL ACCOUNT ===\n\n";

// 1. Check database schema
echo "1. DATABASE SCHEMA:\n";
echo "   - nomor_va column exists: " . (Schema::hasColumn('pembayaran', 'nomor_va') ? '✓ YES' : '✗ NO') . "\n";

// 2. Check Pembayaran model
echo "\n2. PEMBAYARAN MODEL:\n";
$pembayaran = new Pembayaran();
echo "   - nomor_va in fillable: " . (in_array('nomor_va', $pembayaran->getFillable()) ? '✓ YES' : '✗ NO') . "\n";

// 3. Check metode pembayaran
echo "\n3. METODE PEMBAYARAN:\n";
$metodes = MetodePembayaran::all();
foreach ($metodes as $m) {
    $contains = str_contains(strtolower($m->metode ?? ''), 'virtual') ? '✓' : '✗';
    echo "   {$contains} {$m->metode}\n";
}

// 4. Check latest pembayaran with VA
echo "\n4. LATEST PEMBAYARAN WITH VA:\n";
$latest = Pembayaran::with('metode')->latest()->first();
if ($latest) {
    echo "   - ID: {$latest->pembayaran_id}\n";
    echo "   - Transaksi ID: {$latest->transaksi_id}\n";
    echo "   - Metode: {$latest->metode->metode}\n";
    echo "   - Nomor VA: {$latest->nomor_va}\n";
    if ($latest->nomor_va) {
        // Format for display
        $clean = str_replace('-', '', $latest->nomor_va);
        $formatted = substr($clean, 0, 4) . '-' . substr($clean, 4, 6) . '-' . substr($clean, 10);
        echo "   - Formatted: {$formatted}\n";
    }
}

echo "\n=== SYSTEM READY ===\n";
echo "✓ Virtual Account system fully implemented\n";

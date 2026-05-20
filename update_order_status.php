<?php

// Load Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaksi;

// Get the logged-in user's pengguna_id (1 is typically the test user)
$pengunaId = 1;

// Get first order for the user and update it
$transaksi = Transaksi::where('pengguna_id', $pengunaId)->first();
if ($transaksi) {
    echo "Found order: {$transaksi->kode_transaksi}\n";
    echo "Current status: {$transaksi->status}\n";
    $transaksi->status = 'selesai';
    $transaksi->save();
    echo "Updated order {$transaksi->kode_transaksi} to selesai status\n";
} else {
    echo "No transaksi found for pengguna_id=$pengunaId\n";
    // List all transaksi with their pengguna_id
    $allOrders = Transaksi::all();
    foreach ($allOrders as $order) {
        echo "Order: {$order->kode_transaksi} -> pengguna_id: {$order->pengguna_id}\n";
    }
}

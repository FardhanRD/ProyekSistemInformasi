<?php

// Load Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pengguna;
use App\Models\Transaksi;

// Get fairuz
$pengguna = Pengguna::where('nama_pengguna', 'fairuz')->first();

if ($pengguna) {
    echo "User: {$pengguna->nama_pengguna} (ID: {$pengguna->pengguna_id})\n";
    
    // Update first order to selesai
    $order = Transaksi::where('pengguna_id', $pengguna->pengguna_id)->first();
    if ($order) {
        echo "Updating order {$order->kode_transaksi} to selesai\n";
        $order->status = 'selesai';
        $order->save();
        echo "Updated successfully\n";
    }
} else {
    echo "No user found\n";
}

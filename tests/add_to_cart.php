<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use App\Models\Keranjang;
use App\Models\DetailProduk;

$user = Pengguna::where('email','test@example.com')->first();
if(! $user){ echo "No test user\n"; exit(1); }
$detail = DetailProduk::first();
if(! $detail){ echo "No detail_produk\n"; exit(1); }

$k = Keranjang::create([
    'pengguna_id' => $user->pengguna_id,
    'detail_produk_id' => $detail->detail_produk_id,
    'jumlah' => 1,
    'catatan' => 'test add',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Keranjang added: {$k->keranjang_id}\n";

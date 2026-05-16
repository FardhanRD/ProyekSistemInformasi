<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use App\Models\Keranjang;

$u = Pengguna::where('email','test@example.com')->first();
if(! $u) { echo "No user\n"; exit(1); }

echo "user id: {$u->pengguna_id}\n";
$items = Keranjang::where('pengguna_id', $u->pengguna_id)->get();
echo "Keranjang count: " . $items->count() . "\n";
foreach($items as $it) {
    echo "id={$it->keranjang_id} detail_produk_id={$it->detail_produk_id} qty={$it->jumlah}\n";
}

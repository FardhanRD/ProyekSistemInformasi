<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
$admins = Pengguna::where('role','admin')->get();
if ($admins->isEmpty()) {
    echo "No admin users found.\n";
    exit(0);
}
foreach ($admins as $a) {
    echo "pengguna_id: {$a->pengguna_id} | email: {$a->email} | username: {$a->username}\n";
}

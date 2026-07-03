<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\Pengguna;

// Aktifkan semua akun dengan role 'buyer'
Pengguna::where('role', 'buyer')->update(['is_active' => 1]);

echo "All buyer customers activated successfully.\n";

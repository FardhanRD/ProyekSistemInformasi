<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

$email = 'admin@example.com';
$user = Pengguna::where('email', $email)->first();
if (! $user) {
    echo "Admin user not found for email: $email\n";
    exit(1);
}

$passwords = ['admin123', 'password', 'password123!','password123','secret123'];
foreach ($passwords as $p) {
    $ok = Hash::check($p, $user->sandi) ? 'MATCH' : 'NO';
    echo "Check password '{$p}': {$ok}\n";
}

echo "Current hashed password (truncated): " . substr($user->sandi,0,30) . "...\n";

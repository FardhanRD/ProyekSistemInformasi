<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

$email = 'admin@example.com';
$new = 'admin123';
$user = Pengguna::where('email', $email)->first();
if (! $user) {
    echo "Admin user not found for email: $email\n";
    exit(1);
}
$user->sandi = Hash::make($new);
$user->save();

echo "Updated admin ({$email}) password to: {$new}\n";

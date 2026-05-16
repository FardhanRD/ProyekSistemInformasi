<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pengguna;
use App\Models\Buyer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$pw = password_hash('secret123', PASSWORD_BCRYPT);
$user = Pengguna::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'nama_pengguna' => 'Tester',
        'username' => 'tester',
        'email' => 'test@example.com',
        'no_telepon' => '081234567890',
        'sandi' => $pw,
        'role' => 'buyer',
        'is_active' => 1
    ]
);
$buyerPayload = ['pengguna_id' => $user->pengguna_id];
if (Schema::hasColumn('buyer', 'user_id')) {
    $legacyUserId = DB::table('users')->where('email', $user->email)->value('id');

    if (! $legacyUserId) {
        $legacyUserId = DB::table('users')->insertGetId([
            'name' => $user->nama_pengguna,
            'username' => $user->username,
            'email' => $user->email,
            'no_telepon' => $user->no_telepon,
            'password' => $user->sandi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $buyerPayload['user_id'] = $legacyUserId;
}

Buyer::updateOrCreate(['pengguna_id' => $user->pengguna_id], $buyerPayload);

echo "Created user: " . $user->pengguna_id . " (email=test@example.com, password=secret123)\n";

<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

$user = Pengguna::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'nama_pengguna' => 'Admin Demo',
        'username' => 'admin-demo',
        'no_telepon' => '081234567899',
        'sandi' => Hash::make('admin123'),
        'role' => 'admin',
        'is_active' => 1,
    ]
);

$user->role = 'admin';
$user->sandi = Hash::make('admin123');
$user->is_active = 1;
$user->save();

Admin::firstOrCreate(['pengguna_id' => $user->pengguna_id]);

echo "admin created: {$user->email}\n";

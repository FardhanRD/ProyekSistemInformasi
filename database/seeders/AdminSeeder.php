<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama_pengguna' => 'Administrator',
                'username'      => 'admin',
                'email'         => 'admin@example.com',
                'no_telepon'    => '081234567890',
                'sandi'         => Hash::make('admin123'),
                'role'          => 'admin',
                'is_active'     => 1,
            ]
        );
    }
}
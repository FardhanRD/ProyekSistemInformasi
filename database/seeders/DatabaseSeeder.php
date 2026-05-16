<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'nama_pengguna' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@example.com',
                'no_telepon' => '081111111111',
                'sandi' => Hash::make('password'),
                'role' => 'buyer',
                'is_active' => 1,
            ]
        );

        // initial data
        $this->call([
            \Database\Seeders\AdminSeeder::class,
            \Database\Seeders\KategoriSeeder::class,
            \Database\Seeders\WarnaProdukSeeder::class,
            \Database\Seeders\MetodePembayaranSeeder::class,
            \Database\Seeders\EkspedisiSeeder::class,
            \Database\Seeders\BuyerSeeder::class,
            \Database\Seeders\SupplierSeeder::class,
            \Database\Seeders\VoucherSeeder::class,
            \Database\Seeders\DemoFashionSeeder::class,
        ]);
    }
}

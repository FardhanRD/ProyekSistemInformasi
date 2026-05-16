<?php

namespace Database\Seeders;

use App\Services\PenggunaSyncService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BuyerSeeder extends Seeder
{
    public function run(PenggunaSyncService $penggunaSyncService): void
    {
        if (! Schema::hasTable('buyer')) {
            return;
        }

        if (! Schema::hasTable('pengguna')) {
            return;
        }

        $pengguna = DB::table('pengguna')->where('email', 'test@example.com')->first();
        if (! $pengguna) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama_pengguna' => 'Test Buyer',
                'username' => 'test-buyer',
                'email' => 'test@example.com',
                'no_telepon' => '08123456789',
                'sandi' => bcrypt('password'),
                'role' => 'buyer',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $penggunaId = $pengguna->pengguna_id;
        }

        if (DB::table('buyer')->where('pengguna_id', $penggunaId)->doesntExist()) {
            DB::table('buyer')->insert([
                'pengguna_id' => $penggunaId,
                'created_at' => now(),
            ]);
        }
    }
}

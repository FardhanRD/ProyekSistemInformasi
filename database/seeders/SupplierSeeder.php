<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('supplier')) {
            return;
        }

        if (! Schema::hasTable('pengguna')) {
            return;
        }

        $pengguna = DB::table('pengguna')->where('email', 'supplier@example.com')->first();
        if (! $pengguna) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama_pengguna' => 'Test Supplier',
                'username' => 'test-supplier',
                'email' => 'supplier@example.com',
                'no_telepon' => '081222222222',
                'sandi' => Hash::make('password'),
                'role' => 'supplier',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $penggunaId = $pengguna->pengguna_id;
        }

        if (DB::table('supplier')->where('pengguna_id', $penggunaId)->doesntExist()) {
            DB::table('supplier')->insert([
                'pengguna_id' => $penggunaId,
                'nama_toko' => 'MOVR Official Store',
                'nama_owner' => 'Test User',
                'alamat_toko' => 'Jakarta, Indonesia',
                'deskripsi_toko' => 'Toko contoh untuk demo halaman.',
                'is_verified' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

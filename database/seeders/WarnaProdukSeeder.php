<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarnaProdukSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama_warna' => 'Putih', 'kode_hex' => '#FFFFFF'],
            ['nama_warna' => 'Hitam', 'kode_hex' => '#111111'],
            ['nama_warna' => 'Biru', 'kode_hex' => '#1D4ED8'],
            ['nama_warna' => 'Merah', 'kode_hex' => '#DC2626'],
        ];

        // Skema DB bisa berubah (produk_id kadang masih ada, kadang sudah di-drop).
        // Seeder harus adaptif: jika kolom produk_id ada, isi; jika tidak, cukup insert kode_hex.
        $hasProdukIdColumn = \Illuminate\Support\Facades\Schema::hasColumn('warna_produk', 'produk_id');
        $firstProdukId = null;
        if ($hasProdukIdColumn && \Illuminate\Support\Facades\Schema::hasTable('produk')) {
            $firstProdukId = DB::table('produk')->value('produk_id');
        }

        foreach ($items as $item) {
            $data = [
                'kode_hex' => $item['kode_hex'],
            ];
            if ($hasProdukIdColumn) {
                $data['produk_id'] = $firstProdukId;
            }

            DB::table('warna_produk')->updateOrInsert(
                ['nama_warna' => $item['nama_warna']],
                $data
            );
        }

    }
}

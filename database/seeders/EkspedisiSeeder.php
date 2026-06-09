<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EkspedisiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama_ekspedisi' => 'JNE', 'jenis_layanan' => 'REG', 'estimasi_hari' => '2-3 hari', 'ongkir_flat' => 15000],
            ['nama_ekspedisi' => 'JNE', 'jenis_layanan' => 'YES', 'estimasi_hari' => '1 hari', 'ongkir_flat' => 25000],
            ['nama_ekspedisi' => 'J&T', 'jenis_layanan' => 'EZ', 'estimasi_hari' => '2-3 hari', 'ongkir_flat' => 13000],
            ['nama_ekspedisi' => 'SiCepat', 'jenis_layanan' => 'HALU', 'estimasi_hari' => '1 hari', 'ongkir_flat' => 20000],
            ['nama_ekspedisi' => 'Anteraja', 'jenis_layanan' => 'Reguler', 'estimasi_hari' => '2-4 hari', 'ongkir_flat' => 12000],
            ['nama_ekspedisi' => 'GoSend', 'jenis_layanan' => 'Sameday', 'estimasi_hari' => 'Hari ini', 'ongkir_flat' => 30000],
        ];

        foreach ($items as $item) {
            DB::table('ekspedisi')->updateOrInsert(
                ['nama_ekspedisi' => $item['nama_ekspedisi'], 'jenis_layanan' => $item['jenis_layanan']],
                [
                    'estimasi_hari' => $item['estimasi_hari'],
                    'ongkir_flat' => $item['ongkir_flat'],
                ]
            );
        }
    }
}

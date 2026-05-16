<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EkspedisiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama_ekspedisi' => 'JNE', 'jenis_layanan' => 'REG', 'estimasi_hari' => '2-3 hari'],
            ['nama_ekspedisi' => 'JNE', 'jenis_layanan' => 'YES', 'estimasi_hari' => '1 hari'],
            ['nama_ekspedisi' => 'J&T', 'jenis_layanan' => 'EZ', 'estimasi_hari' => '2-3 hari'],
            ['nama_ekspedisi' => 'SiCepat', 'jenis_layanan' => 'HALU', 'estimasi_hari' => '1 hari'],
            ['nama_ekspedisi' => 'Anteraja', 'jenis_layanan' => 'Reguler', 'estimasi_hari' => '2-4 hari'],
            ['nama_ekspedisi' => 'GoSend', 'jenis_layanan' => 'Sameday', 'estimasi_hari' => 'Hari ini'],
        ];

        foreach ($items as $item) {
            DB::table('ekspedisi')->updateOrInsert(
                ['nama_ekspedisi' => $item['nama_ekspedisi'], 'jenis_layanan' => $item['jenis_layanan']],
                ['estimasi_hari' => $item['estimasi_hari']]
            );
        }
    }
}

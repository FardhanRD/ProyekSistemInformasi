<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['metode' => 'BCA', 'jenis' => 'transfer'],
            ['metode' => 'Mandiri', 'jenis' => 'transfer'],
            ['metode' => 'BNI', 'jenis' => 'transfer'],
            ['metode' => 'GoPay', 'jenis' => 'ewallet'],
            ['metode' => 'OVO', 'jenis' => 'ewallet'],
            ['metode' => 'Dana', 'jenis' => 'ewallet'],
            ['metode' => 'QRIS', 'jenis' => 'qris'],
            ['metode' => 'COD', 'jenis' => 'cod'],
        ];

        foreach ($items as $item) {
            DB::table('metode_pembayaran')->updateOrInsert(
                ['metode' => $item['metode'], 'jenis' => $item['jenis']],
                ['is_active' => 1]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('voucher')->updateOrInsert(
            ['kode_voucher' => 'DISC10'],
            [
                'nama_voucher' => 'Diskon 10%',
                'deskripsi' => 'Voucher contoh 10% untuk belanja minimal 100rb.',
                'jenis_diskon' => 'persen',
                'nilai_diskon' => 10,
                'min_belanja' => 100000,
                'maks_diskon' => 50000,
                'kuota' => 100,
                'berlaku_mulai' => now(),
                'berlaku_sampai' => now()->addMonth(),
                'is_active' => 1,
            ]
        );
    }
}

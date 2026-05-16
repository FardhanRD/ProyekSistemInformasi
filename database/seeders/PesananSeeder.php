<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first transaksi and ekspedisi
        $transaksi = DB::table('transaksi')->first();
        $ekspedisi = DB::table('ekspedisi')->first();

        if (!$transaksi || !$ekspedisi) {
            return; // Skip if no transaksi or ekspedisi
        }

        // Check if pesanan for this transaksi already exists
        $existing = DB::table('pesanan')->where('transaksi_id', $transaksi->transaksi_id)->first();
        if ($existing) {
            return;
        }

        DB::table('pesanan')->insert([
            'transaksi_id' => $transaksi->transaksi_id,
            'ekspedisi_id' => $ekspedisi->ekspedisi_id,
            'no_resi' => 'JNE' . strtoupper(substr(md5(rand()), 0, 10)),
            'status_pesanan' => 'diterima',
            'alamat_pengiriman' => 'Alamat pengiriman sample',
            'foto_bukti' => null,
            'waktu_diambil' => now()->subDays(2),
            'estimasi_tiba' => now()->addDays(3),
            'created_at' => now()->subDays(2),
            'updated_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrackingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first pesanan if exists
        $pesanan = DB::table('pesanan')->first();

        if (!$pesanan) {
            return; // Skip if no pesanan
        }

        $logs = [
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Pesanan Dikonfirmasi',
                'deskripsi' => 'Pesanan Anda telah dikonfirmasi oleh penjual',
                'lokasi' => 'Gudang Penjual',
                'waktu_update' => now()->subHours(48),
            ],
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Sedang Dikemas',
                'deskripsi' => 'Produk sedang dikemas oleh penjual',
                'lokasi' => 'Gudang Penjual',
                'waktu_update' => now()->subHours(36),
            ],
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Siap Dikirim',
                'deskripsi' => 'Paket siap dikirim ke kurir',
                'lokasi' => 'Hub Asal',
                'waktu_update' => now()->subHours(24),
            ],
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Dalam Pengiriman',
                'deskripsi' => 'Paket sudah dijemput oleh kurir dan dalam perjalanan',
                'lokasi' => 'Dalam Perjalanan',
                'waktu_update' => now()->subHours(12),
            ],
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Tiba di Tujuan',
                'deskripsi' => 'Paket sudah tiba di kota tujuan',
                'lokasi' => 'Hub Tujuan',
                'waktu_update' => now()->subHours(2),
            ],
            [
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Diterima',
                'deskripsi' => 'Paket telah diterima oleh penerima',
                'lokasi' => 'Alamat Penerima',
                'waktu_update' => now(),
            ],
        ];

        foreach ($logs as $log) {
            DB::table('tracking_log')->insertOrIgnore($log);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Buyer;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Produk;
use App\Models\DetailProduk;
use App\Models\AlamatPengguna;
use Illuminate\Support\Facades\Schema;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('transaksi') || !Schema::hasTable('buyer')) {
            return;
        }

        // Get first buyer (created from user id=1 via BuyerSeeder)
        $buyer = Buyer::where('pengguna_id', 1)->first();
        if (!$buyer) {
            return;
        }

        // Create or get sample address
        $alamat = AlamatPengguna::where('pengguna_id', 1)->first();
        if (!$alamat) {
            $alamat = AlamatPengguna::create([
                'pengguna_id' => 1,
                'label' => 'Rumah',
                'nama_penerima' => 'Test User',
                'no_telepon' => '081234567890',
                'provinsi' => 'DKI Jakarta',
                'kota' => 'Jakarta Pusat',
                'kecamatan' => 'Menteng',
                'kelurahan' => 'Cikini',
                'kode_pos' => '10330',
                'alamat_lengkap' => 'Jl. Test No. 123, Jakarta',
                'is_utama' => 1
            ]);
        }

        // Get sample products
        $produk = Produk::first();
        if (!$produk) {
            return;
        }

        $detail = DetailProduk::where('produk_id', $produk->produk_id)->first();
        if (!$detail) {
            return;
        }

        // Create sample transaksi
        $transaksi = Transaksi::create([
            'buyer_id' => $buyer->buyer_id,
            'alamat_id' => $alamat->alamat_id,
            'ekspedisi_id' => null,
            'voucher_id' => null,
            'kode_transaksi' => 'TRX' . now()->timestamp,
            'subtotal' => 350000,
            'diskon_voucher' => 0,
            'ongkos_kirim' => 25000,
            'total_harga' => 375000,
            'status' => 'menunggu_pembayaran',
            'tanggal' => now()
        ]);

        // Create transaksi detail
        TransaksiDetail::create([
            'transaksi_id' => $transaksi->transaksi_id,
            'detail_produk_id' => $detail->detail_produk_id,
            'nama_produk_snap' => $produk->nama_produk,
            'harga_snap' => 350000,
            'ukuran_snap' => $detail->ukuran,
            'warna_snap' => null,
            'quantity' => 1,
            'subtotal' => 350000
        ]);
    }
}

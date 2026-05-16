<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // create 3 sample products with details and images
        $now = now();
        $warnaIds = DB::table('warna_produk')->pluck('warna_id')->values();
        $products = [
            ['nama_produk'=>'Sneakers Putih','slug'=>'sneakers-putih','deskripsi'=>'Sepatu sneakers putih nyaman untuk sehari-hari.','harga_dasar'=>350000,'kategori_id'=>4,'supplier_id'=>1],
            ['nama_produk'=>'Kaos Polos Hitam','slug'=>'kaos-polos-hitam','deskripsi'=>'Kaos katun hitam basic.','harga_dasar'=>75000,'kategori_id'=>4,'supplier_id'=>1],
            ['nama_produk'=>'Tas Ransel','slug'=>'tas-ransel','deskripsi'=>'Ransel multifungsi 20L.','harga_dasar'=>250000,'kategori_id'=>7,'supplier_id'=>1],
        ];

        foreach ($products as $i => $p) {
            // skip if slug already exists
            if (DB::table('produk')->where('slug', $p['slug'])->exists()) {
                continue;
            }
            // mark first product as featured
            if ($i === 0) $p['is_featured'] = 1;
            $id = DB::table('produk')->insertGetId($p);
            $variantRows = [
                ['produk_id' => $id, 'warna_id' => $warnaIds[0] ?? null, 'nama_produk' => $p['nama_produk'], 'ukuran' => 'M', 'harga' => $p['harga_dasar'], 'stok' => 10, 'sku' => Str::upper(Str::random(6))],
                ['produk_id' => $id, 'warna_id' => $warnaIds[1] ?? null, 'nama_produk' => $p['nama_produk'], 'ukuran' => 'L', 'harga' => $p['harga_dasar'] + 15000, 'stok' => 8, 'sku' => Str::upper(Str::random(6))],
            ];

            DB::table('detail_produk')->insert($variantRows);
            DB::table('gambar_produk')->insert([
                ['produk_id'=>$id,'url_gambar'=>'https://via.placeholder.com/400x300?text='.urlencode($p['nama_produk']),'alt_text'=>$p['nama_produk'],'urutan'=>0]
            ]);
        }
    }
}

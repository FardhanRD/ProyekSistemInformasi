<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama
        DB::table('kategori')->delete();

        // LEVEL 1 — Parent Utama
        DB::table('kategori')->insert([
            ['nama_kategori'=>'MAN',   'slug'=>'man',   'parent_id'=>null,'level'=>1,'urutan'=>1,'is_active'=>1],
            ['nama_kategori'=>'WOMEN', 'slug'=>'women', 'parent_id'=>null,'level'=>1,'urutan'=>2,'is_active'=>1],
            ['nama_kategori'=>'KIDS',  'slug'=>'kids',  'parent_id'=>null,'level'=>1,'urutan'=>3,'is_active'=>1],
        ]);

        $man   = DB::table('kategori')->where('slug','man')->value('kategori_id');
        $women = DB::table('kategori')->where('slug','women')->value('kategori_id');
        $kids  = DB::table('kategori')->where('slug','kids')->value('kategori_id');

        // LEVEL 2 — Sub Kategori
        DB::table('kategori')->insert([

            // MAN
            ['nama_kategori'=>'Clothing',    'slug'=>'man-clothing',    'parent_id'=>$man,'level'=>2,'urutan'=>1,'is_active'=>1],
            ['nama_kategori'=>'Accessories', 'slug'=>'man-accessories', 'parent_id'=>$man,'level'=>2,'urutan'=>2,'is_active'=>1],
            ['nama_kategori'=>'Sale',        'slug'=>'man-sale',        'parent_id'=>$man,'level'=>2,'urutan'=>3,'is_active'=>1],

            // WOMEN
            ['nama_kategori'=>'Clothing',    'slug'=>'women-clothing',    'parent_id'=>$women,'level'=>2,'urutan'=>1,'is_active'=>1],
            ['nama_kategori'=>'Accessories', 'slug'=>'women-accessories', 'parent_id'=>$women,'level'=>2,'urutan'=>2,'is_active'=>1],
            ['nama_kategori'=>'Sale',        'slug'=>'women-sale',        'parent_id'=>$women,'level'=>2,'urutan'=>3,'is_active'=>1],

            // KIDS
            ['nama_kategori'=>'Clothing',    'slug'=>'kids-clothing',    'parent_id'=>$kids,'level'=>2,'urutan'=>1,'is_active'=>1],
            ['nama_kategori'=>'Accessories', 'slug'=>'kids-accessories', 'parent_id'=>$kids,'level'=>2,'urutan'=>2,'is_active'=>1],
            ['nama_kategori'=>'Sale',        'slug'=>'kids-sale',        'parent_id'=>$kids,'level'=>2,'urutan'=>3,'is_active'=>1],
        ]);
    }
}
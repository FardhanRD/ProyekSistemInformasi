<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoFashionSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('produk') || ! Schema::hasTable('detail_produk')) {
            return;
        }

        $buyer = DB::table('pengguna')->where('email', 'test@example.com')->first();
        $supplier = DB::table('supplier')->where('email', 'supplier@example.com')->first();
        if (! $supplier) {
            $supplier = DB::table('supplier')->first();
        }
        $bca = DB::table('metode_pembayaran')->where('metode', 'BCA')->first();
        $jneReg = DB::table('ekspedisi')->where('nama_ekspedisi', 'JNE')->where('jenis_layanan', 'REG')->first();

        if (! $buyer || ! $supplier || ! $bca || ! $jneReg) {
            return;
        }

        $kategoriMap = [
            'man-clothing' => DB::table('kategori')->where('slug', 'man-clothing')->value('kategori_id'),
            'man-accessories' => DB::table('kategori')->where('slug', 'man-accessories')->value('kategori_id'),
            'women-clothing' => DB::table('kategori')->where('slug', 'women-clothing')->value('kategori_id'),
            'women-accessories' => DB::table('kategori')->where('slug', 'women-accessories')->value('kategori_id'),
        ];

        $products = [
            [
                'slug' => '3-stripes-tshirt-beige',
                'nama_produk' => '3-Stripes T-Shirt Beige',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Kaos 3-Stripes warna beige dengan opsi warna varian Blue dan White.',
                'harga_dasar' => 229000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2dc7bcbdfbfa4cca8bc5f1ad34b9a571_9366/3-Stripes_T-Shirt_Beige_KE7964_21_model.jpg',
                    'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/e4492c8c99cf4cd5ad7f8b5158a3960c_9366/3-Stripes_T-Shirt_Beige_KE7964_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/2bfda20fc36b41aeb7feec38a7e9d51b_9366/3-Stripes_T-Shirt_Beige_KE7964_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TSHIRT-KE7964-BEIGE-S', 'label' => 'Beige - S', 'ukuran' => 'S', 'warna' => 'Beige', 'harga' => 229000, 'stok' => 10, 'berat_gram' => 220, 'image' => null],
                    ['sku' => 'MOVR-TSHIRT-KE3536-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 229000, 'stok' => 8, 'berat_gram' => 220, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/608bd680d44649b38cc37c1d14d2a49b_9366/3-Stripes_T-Shirt_Blue_KE3536_21_model.jpg'],
                    ['sku' => 'MOVR-TSHIRT-KE3537-WHITE-L', 'label' => 'White - L', 'ukuran' => 'L', 'warna' => 'White', 'harga' => 229000, 'stok' => 5, 'berat_gram' => 220, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/70a6980f8aab46d981b2caff95cc1ffb_9366/3-Stripes_T-Shirt_White_KE3537_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'ultimate365-jacquard-polo-blue',
                'nama_produk' => 'ULTIMATE365 Jacquard Polo Shirt Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Polo jacquard warna biru untuk kebutuhan casual dan sport.',
                'harga_dasar' => 249000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7a6d9ddff1c54d899bde7373239e1cfb_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/119037ca2d03409092b564d9962380af_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc0aae8365cf4a98b07192685de4c1b1_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-POLO-KB1394-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 249000, 'stok' => 7, 'berat_gram' => 240, 'image' => null],
                    ['sku' => 'MOVR-POLO-KB1393-GREEN-L', 'label' => 'Green - L', 'ukuran' => 'L', 'warna' => 'Green', 'harga' => 249000, 'stok' => 4, 'berat_gram' => 240, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ddaf219ad327457bba9f592d13bbd144_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Green_KB1393_21_model.jpg'],
                    ['sku' => 'MOVR-POLO-KC3535-PINK-S', 'label' => 'Pink - S', 'ukuran' => 'S', 'warna' => 'Pink', 'harga' => 249000, 'stok' => 3, 'berat_gram' => 240, 'image' => 'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/36bb1d570258471aa77be9198d6f5081_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Pink_KC3535_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'washed-cali-tee-blue',
                'nama_produk' => 'WASHED CALI TEE Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'T-shirt washed style dengan warna utama biru dan varian pink.',
                'harga_dasar' => 209000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2be173dab8e04f2589a2822dba6320e2_9366/WASHED_CALI_TEE_Blue_KX1261_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/382b9b25dc1a4e32b1f4f0f410a89688_9366/WASHED_CALI_TEE_Blue_KX1261_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/56bae6e54ddc4b63a7087483e8b22a2d_9366/WASHED_CALI_TEE_Blue_KX1261_41_detail.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-CALI-KX1261-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 209000, 'stok' => 9, 'berat_gram' => 210, 'image' => null],
                    ['sku' => 'MOVR-CALI-KX1259-PINK-S', 'label' => 'Pink - S', 'ukuran' => 'S', 'warna' => 'Pink', 'harga' => 209000, 'stok' => 6, 'berat_gram' => 210, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a1ac3eee340d49a2b6cc470460d06f96_9366/WASHED_CALI_TEE_Pink_KX1259_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'y-3-ft-hoodie-brown',
                'nama_produk' => 'Y-3 FT Hoodie Brown',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie Y-3 FT warna brown dengan opsi varian black dan purple.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/340e096532c244e684dc36f5f63ce6f0_9366/Y-3_FT_Hoodie_Brown_KS5430_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e7c17f69224450fad455869f70c947a_9366/Y-3_FT_Hoodie_Brown_KS5430_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/79418cbe6e974465a565af7e8fea7299_9366/Y-3_FT_Hoodie_Brown_KS5430_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-HOODIE-KS5430-BROWN-M', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 459000, 'stok' => 6, 'berat_gram' => 620, 'image' => null],
                    ['sku' => 'MOVR-HOODIE-KA3112-BLACK-L', 'label' => 'Black - L', 'ukuran' => 'L', 'warna' => 'Black', 'harga' => 459000, 'stok' => 2, 'berat_gram' => 620, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5925658e81f648e2a23cea0c923d89db_9366/Y-3_FT_Hoodie_Black_KA3112_21_model.jpg'],
                    ['sku' => 'MOVR-HOODIE-KA3113-PURPLE-S', 'label' => 'Purple - S', 'ukuran' => 'S', 'warna' => 'Purple', 'harga' => 459000, 'stok' => 4, 'berat_gram' => 620, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/249e959e48a74166ab965f7662b2352b_9366/Y-3_FT_Hoodie_Purple_KA3113_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'denim-chino-shorts-blue',
                'nama_produk' => 'DENIM CHINO SHORTS Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Celana pendek denim chino untuk gaya santai.',
                'harga_dasar' => 189000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e3a2f95c25064508b41344eea9e1d5b6_9366/DENIM_CHINO_SHORTS_Blue_KX1192_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/808a909906dd4e33b16f7afbe97284f3_9366/DENIM_CHINO_SHORTS_Blue_KX1192_25_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7f7000a456e247b0adef839ce9d5aeb0_9366/DENIM_CHINO_SHORTS_Blue_KX1192_01_laydown.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-SHORTS-KX1192-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 189000, 'stok' => 10, 'berat_gram' => 280, 'image' => null],
                    ['sku' => 'MOVR-SHORTS-KX1192-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 189000, 'stok' => 7, 'berat_gram' => 280, 'image' => null],
                ],
            ],
            [
                'slug' => 'song-for-the-mute-007-track-top-brown',
                'nama_produk' => 'SONG FOR THE MUTE 007 Track Top Brown',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top premium dengan nuansa brown dan varian grey.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KS1339-BROWN-M', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 499000, 'stok' => 5, 'berat_gram' => 650, 'image' => null],
                    ['sku' => 'MOVR-TRACKTOP-KS1340-GREY-L', 'label' => 'Grey - L', 'ukuran' => 'L', 'warna' => 'Grey', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 650, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'summer-glow-three-stripes-tee-white',
                'nama_produk' => 'ADIDAS ORIGINALS Summer Glow Three Stripes Tee White',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'T-shirt women dengan varian warna black.',
                'harga_dasar' => 229000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ea8bc0b3a404cd09a62e6fe4447fe55_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/586c5bfb42b54688936457e524830e2d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6b7a1949f41d45b8830d4d6a39104110_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WTEE-KY8126-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 229000, 'stok' => 8, 'berat_gram' => 200, 'image' => null],
                    ['sku' => 'MOVR-WTEE-KY8127-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 229000, 'stok' => 6, 'berat_gram' => 200, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f245aa400b8c4fa285d228e86194c1d5_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_Black_KY8127_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'power-light-support-bra-tank-purple',
                'nama_produk' => 'Power Light Support Bra Tank Purple',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Bra tank support dengan varian white dan purple.',
                'harga_dasar' => 259000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/63ae819c48254b64a17b3b9c3c7acf6f_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/53c032b1c71f411181eccd6d2b53aae1_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aa18f567cb774b44a5cec45d9615f4f2_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BRA-KD2227-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 259000, 'stok' => 9, 'berat_gram' => 180, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/eb744e7c6cda4252a2303228cff7faae_9366/Power_Light_Support_Bra_Tank_White_JZ6028_21_model.jpg'],
                    ['sku' => 'MOVR-BRA-JZ6029-PURPLE-M', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 259000, 'stok' => 7, 'berat_gram' => 180, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4a93fb502c784846bf7ade917c7f33ae_9366/Power_Light_Support_Bra_Tank_Purple_JZ6029_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'ultimate365-tour-cardigan-blue',
                'nama_produk' => 'ULTIMATE365 Tour Cardigan Blue',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cardigan women untuk lapisan luar yang ringan.',
                'harga_dasar' => 379000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/9ddcb9dfa0f148439fa5171781c97321_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90276d41ebd848538cf9baad8e4379f9_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c22cb25feb7f43aa951d0ae5cb02a145_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-CARDIGAN-JX6659-BLUE-S', 'label' => 'Blue - S', 'ukuran' => 'S', 'warna' => 'Blue', 'harga' => 379000, 'stok' => 5, 'berat_gram' => 450, 'image' => null],
                    ['sku' => 'MOVR-CARDIGAN-KE7781-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 379000, 'stok' => 4, 'berat_gram' => 450, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'summer-glow-shorts-pink',
                'nama_produk' => 'ADIDAS ORIGINALS Summer Glow Shorts Pink',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Shorts women dengan varian brown.',
                'harga_dasar' => 189000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8dcbbeac9a3847a989c1eb0c85e24cfe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/084495938abb4bb5a927402500daddd1_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/06b2066c433643a0b548de19384dd2fe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSHORT-KY3169-PINK-S', 'label' => 'Pink - S', 'ukuran' => 'S', 'warna' => 'Pink', 'harga' => 189000, 'stok' => 11, 'berat_gram' => 220, 'image' => null],
                    ['sku' => 'MOVR-WSHORT-KY3167-BROWN-M', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 189000, 'stok' => 6, 'berat_gram' => 220, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f43e92f052f14849b95a232b02034165_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Brown_KY3167_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'adidas-entire-studios-training-mid-layer-jacket-beige',
                'nama_produk' => 'adidas x entire studios Training Mid layer Jacket Beige',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Jaket mid layer dengan tone beige yang premium.',
                'harga_dasar' => 559000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-JACKET-KD6091-BEIGE-M', 'label' => 'Beige - M', 'ukuran' => 'M', 'warna' => 'Beige', 'harga' => 559000, 'stok' => 4, 'berat_gram' => 700, 'image' => null],
                    ['sku' => 'MOVR-JACKET-KD8507-WHITE-L', 'label' => 'White - L', 'ukuran' => 'L', 'warna' => 'White', 'harga' => 559000, 'stok' => 3, 'berat_gram' => 700, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'tiro-shoebag-black',
                'nama_produk' => 'TIRO Shoebag Black',
                'kategori_slug' => 'man-accessories',
                'deskripsi' => 'Shoebag compact untuk kebutuhan olahraga dan perjalanan.',
                'harga_dasar' => 159000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f2153fb7ba95457f8c96ccc5f98566b2_9366/TIRO_SHOEBAG_Black_JY7993_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f10c27392d24798ab251908d1b0d9fe_9366/TIRO_SHOEBAG_Black_JY7993_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f8286e3ca96497ca21a611ccacaec90_9366/TIRO_SHOEBAG_Black_JY7993_04_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BAG-JY7993-BLACK-OS', 'label' => 'Black - OS', 'ukuran' => 'OS', 'warna' => 'Black', 'harga' => 159000, 'stok' => 12, 'berat_gram' => 150, 'image' => null],
                ],
            ],
            [
                'slug' => 'pet-shoulder-bag-carrier-brown',
                'nama_produk' => 'PET Shoulder Bag Carrier Brown',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Shoulder bag carrier untuk penggunaan harian.',
                'harga_dasar' => 199000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f11691d4081d407db0fdf5c3ea70976e_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/36b6324044ea46ff93da0abb8c9c3691_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_04_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/45d05df146d0487090bd897e252ec09b_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BAG-KY8726-BROWN-OS', 'label' => 'Brown - OS', 'ukuran' => 'OS', 'warna' => 'Brown', 'harga' => 199000, 'stok' => 9, 'berat_gram' => 180, 'image' => null],
                ],
            ],
            [
                'slug' => 'adicolor-mini-bowling-bag-denim',
                'nama_produk' => 'Adicolor Mini Bowling Bag Denim Multicolor',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Mini bowling bag denim multicolor untuk tampilan kasual.',
                'harga_dasar' => 349000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2fefd0546eec4885a55b4db22d0b9752_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/854a5d050aaa47cdb3be2e359715db79_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_04_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/760fc854796f4737adfc45ea1983994c_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BAG-KD7897-DENIM-OS', 'label' => 'Denim - OS', 'ukuran' => 'OS', 'warna' => 'Denim', 'harga' => 349000, 'stok' => 7, 'berat_gram' => 220, 'image' => null],
                ],
            ],
            [
                'slug' => 'knitted-resort-shirt-white',
                'nama_produk' => 'KNITTED RESORT SHIRT White',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Resort shirt ringan untuk tampilan santai.',
                'harga_dasar' => 279000,
                'images' => [
                    'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e963ff943fb47fc8bf5ea2381fd1564_9366/KNITTED_RESORT_SHIRT_White_KX1223_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1e4b699ecf56499ebbe8dac0a5373072_9366/KNITTED_RESORT_SHIRT_White_KX1223_23_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8292735fc7364373a5e542cacf127ddf_9366/KNITTED_RESORT_SHIRT_White_KX1223_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-RESORT-KX1223-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 279000, 'stok' => 6, 'berat_gram' => 260, 'image' => null],
                ],
            ],
            [
                'slug' => 'soft-pique-short-sleeve-polo-blue',
                'nama_produk' => 'SOFT PIQUE Short Sleeve Polo Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Polo shirt pique dengan varian putih dan hitam.',
                'harga_dasar' => 239000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a793751a8cbe475e9104bc154e72ce75_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edb7680323464ab1beaf0d64eff5548d_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6bf08836751049ce898448307e51f957_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-POLO-KB4822-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 239000, 'stok' => 8, 'berat_gram' => 230, 'image' => null],
                    ['sku' => 'MOVR-POLO-JZ4308-WHITE-L', 'label' => 'White - L', 'ukuran' => 'L', 'warna' => 'White', 'harga' => 239000, 'stok' => 5, 'berat_gram' => 230, 'image' => 'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/a052d4a994e24933a05ce66e1ae2a6dd_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_White_JZ4308_21_model.jpg'],
                    ['sku' => 'MOVR-POLO-KB4821-BLACK-S', 'label' => 'Black - S', 'ukuran' => 'S', 'warna' => 'Black', 'harga' => 239000, 'stok' => 4, 'berat_gram' => 230, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4a4e99664cd14caa87feb16c12ea13cb_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Black_KB4821_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'd4t-workout-full-zip-hoodie-blue',
                'nama_produk' => 'D4T Workout Full-Zip Hoodie Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie full zip untuk training.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c392460a8a1d4453a790fe9a3c0c838f_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aac9a8d12bb7438eb38c4a184915f90b_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7e4b9ba2fdf847ce94827127192a983a_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-HOODIE-KA4822-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 499000, 'stok' => 5, 'berat_gram' => 610, 'image' => null],
                ],
            ],
            [
                'slug' => 'denim-jacket-blue',
                'nama_produk' => 'DENIM JACKET Blue',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Jaket denim klasik warna blue.',
                'harga_dasar' => 529000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-JACKET-KR5042-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 529000, 'stok' => 3, 'berat_gram' => 720, 'image' => null],
                ],
            ],
            [
                'slug' => 'printed-seersucker-shorts-white',
                'nama_produk' => 'PRINTED SEERSUCKER SHORTS White',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts seersucker ringan untuk cuaca panas.',
                'harga_dasar' => 179000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/638855057e3a40c5a7b76a02e9e87316_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/42b4d64fd4494ddcbb1dd63e04b981d0_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1c4afe3d416246ce9cdae8d6799f3d11_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_01_laydown.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-SHORTS-KX1229-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 179000, 'stok' => 10, 'berat_gram' => 210, 'image' => null],
                    ['sku' => 'MOVR-SHORTS-KX1228-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 179000, 'stok' => 7, 'berat_gram' => 210, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aff1f1ee3ce248638621d3d4354ec0e1_9366/PRINTED_SEERSUCKER_SHORTS_Blue_KX1228_21_model.jpg'],
                ],
            ],
            [
                'slug' => '3-stripes-loose-engineered-shorts-black',
                'nama_produk' => '3-Stripes Loose Engineered Shorts Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts casual loose fit untuk aktifitas harian.',
                'harga_dasar' => 199000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf5d11c2bae2456eaffea145fea84f1d_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/12096701657c4869857a380a0651ef7c_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc34a690deb84d1c87bd3f08c6bca6b2_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-SHORTS-KE3594-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 199000, 'stok' => 9, 'berat_gram' => 240, 'image' => null],
                    ['sku' => 'MOVR-SHORTS-KE3590-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 199000, 'stok' => 8, 'berat_gram' => 240, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0a17f40b0a994630ace3501901d83818_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Blue_KE3590_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'adi365-cheering-hoodie-purple',
                'nama_produk' => 'adi365 Cheering Hoodie Purple',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Hoodie nyaman untuk daily wear.',
                'harga_dasar' => 449000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-HOODIE-KA0160-PURPLE-M', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 449000, 'stok' => 6, 'berat_gram' => 610, 'image' => null],
                    ['sku' => 'MOVR-HOODIE-KA0331-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 449000, 'stok' => 5, 'berat_gram' => 610, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'jude-bellingham-hoodie-black',
                'nama_produk' => 'Jude Bellingham Hoodie Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie signature dengan style sporty.',
                'harga_dasar' => 479000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66a0d7ee4ed841918af0dc928d28101a_9366/Jude_Bellingham_Hoodie_Black_KD6519_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dc03ac01a204f0182c16c76ce7df2b7_9366/Jude_Bellingham_Hoodie_Black_KD6519_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/86ebeef63aa84907812b66a1a49ed1f9_9366/Jude_Bellingham_Hoodie_Black_KD6519_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-HOODIE-KD6519-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 479000, 'stok' => 4, 'berat_gram' => 630, 'image' => null],
                ],
            ],
            [
                'slug' => 'zne-woven-bomber-white',
                'nama_produk' => 'ADIDAS Z.N.E. Woven Bomber White',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Bomber jacket ringan untuk style modern.',
                'harga_dasar' => 599000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5d17f749e3a545e69ab907a68bf769d4_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_23_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f40899fcc494ee59842d515937cec9f_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BOMBER-KD8507-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 599000, 'stok' => 4, 'berat_gram' => 680, 'image' => null],
                ],
            ],
            [
                'slug' => 'sst-loose-mesh-track-top-black',
                'nama_produk' => 'SST Loose Mesh Track Top Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top mesh dengan fit loose.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7186bd540e274780b106fb151fd2c7dd_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8133dd62c37d4e64849ae336aa215628_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM3_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6f354a2bea214c20a8bacc3c29c53c85_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KE0115-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 459000, 'stok' => 5, 'berat_gram' => 560, 'image' => null],
                ],
            ],
            [
                'slug' => 'song-for-the-mute-007-track-top-brown-men',
                'nama_produk' => 'SONG FOR THE MUTE 007 Track Top Brown Men',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top premium untuk pria dengan varian grey.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KS1339-BROWN-M2', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                    ['sku' => 'MOVR-TRACKTOP-KS1340-GREY-L2', 'label' => 'Grey - L', 'ukuran' => 'L', 'warna' => 'Grey', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'song-for-the-mute-007-track-top-brown-women',
                'nama_produk' => 'SONG FOR THE MUTE 007 Track Top Brown Women',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Track top dengan tone brown dan grey untuk women.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KS1339-BROWN-W', 'label' => 'Brown - S', 'ukuran' => 'S', 'warna' => 'Brown', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                    ['sku' => 'MOVR-TRACKTOP-KS1340-GREY-W', 'label' => 'Grey - M', 'ukuran' => 'M', 'warna' => 'Grey', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-summer-glow-striped-cropped-polo-pink',
                'nama_produk' => 'ADIDAS Originals Summer Glow Striped Cropped Polo Pink',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cropped polo striped dengan varian orange.',
                'harga_dasar' => 239000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b91205afafb149f3ae391925f7c8600c_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6a70777d20934e5a8b8809767301b893_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cb476cb621164e2b844f2053d7f6971f_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WPOLO-KY8140-PINK-S', 'label' => 'Pink - S', 'ukuran' => 'S', 'warna' => 'Pink', 'harga' => 239000, 'stok' => 6, 'berat_gram' => 210, 'image' => null],
                    ['sku' => 'MOVR-WPOLO-KY8139-ORANGE-M', 'label' => 'Orange - M', 'ukuran' => 'M', 'warna' => 'Orange', 'harga' => 239000, 'stok' => 5, 'berat_gram' => 210, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3eacf47e357e49bbbf00fe657f03898d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Orange_KY8139_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-summer-glow-graphics-tee-white',
                'nama_produk' => 'ADIDAS Originals Summer Glow Graphics Tee White',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Graphics tee dengan gaya summer glow.',
                'harga_dasar' => 219000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/74c27d262c0d4bd1b2d4286dbce6b040_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/815cb89983aa41a0b0ecae2691a770a6_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f540eef8746248d4bfdd369d61c0e0ef_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WTEE-KY8142-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 219000, 'stok' => 7, 'berat_gram' => 190, 'image' => null],
                    ['sku' => 'MOVR-WTEE-KY8143-PINK-M', 'label' => 'Pink - M', 'ukuran' => 'M', 'warna' => 'Pink', 'harga' => 219000, 'stok' => 6, 'berat_gram' => 190, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6df9d5690ae442379fcfe1e6400d5f64_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_Pink_KY8143_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-summer-glow-vintage-tee-blue',
                'nama_produk' => 'ADIDAS Originals Summer Glow Vintage Tee Blue',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Vintage tee dengan nuansa casual.',
                'harga_dasar' => 209000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3990a22151994ba9aed77f80d9000290_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/d964a22eb2da4c5a823bbe92ae859ff0_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5b0e42ead43b4b3f9d6bfd43bb63918b_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WTEE-KY8129-BLUE-S', 'label' => 'Blue - S', 'ukuran' => 'S', 'warna' => 'Blue', 'harga' => 209000, 'stok' => 5, 'berat_gram' => 185, 'image' => null],
                    ['sku' => 'MOVR-WTEE-KY8130-PINK-M', 'label' => 'Pink - M', 'ukuran' => 'M', 'warna' => 'Pink', 'harga' => 209000, 'stok' => 4, 'berat_gram' => 185, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a6218aab06be4995b7493c52d3d51702_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Pink_KY8130_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-summer-glow-mesh-graphics-long-sleeve-white',
                'nama_produk' => 'ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Long sleeve mesh graphics untuk gaya layer.',
                'harga_dasar' => 229000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f56c37da36644f78967a5fb52688eab_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/effed0d5433a4c7c98d5720f05a2763d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/55b8f11611a34127afd9345611396b96_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WLS-KY8138-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 229000, 'stok' => 6, 'berat_gram' => 195, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-satin-lace-shorts-blue',
                'nama_produk' => 'ADIDAS Originals Satin Lace Shorts Blue',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Shorts satin lace dengan varian burgundy dan blue.',
                'harga_dasar' => 189000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c3095b58067047fb99fd4ab1593ce068_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90c2c1e7a74d47a19808e12757361523_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b6ec198f7af44a07bac5facb9636c1dc_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSHORT-LD1812-BLUE-S', 'label' => 'Blue - S', 'ukuran' => 'S', 'warna' => 'Blue', 'harga' => 189000, 'stok' => 8, 'berat_gram' => 200, 'image' => null],
                    ['sku' => 'MOVR-WSHORT-LD1810-BURGUNDY-M', 'label' => 'Burgundy - M', 'ukuran' => 'M', 'warna' => 'Burgundy', 'harga' => 189000, 'stok' => 6, 'berat_gram' => 200, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b28ebfb5af88494eb2ddb6815ec91868_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Burgundy_LD1810_21_model.jpg'],
                    ['sku' => 'MOVR-WSHORT-LD1811-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 189000, 'stok' => 5, 'berat_gram' => 200, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1fbd36752eb34528bc5297c693264d09_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1811_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-adi365-running-2-in-1-shorts-purple',
                'nama_produk' => 'Adi365 H.Koumori Running 2-In-1 Shorts Purple',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Running shorts 2-in-1 untuk kebutuhan aktif.',
                'harga_dasar' => 279000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/324f65ca0c1b414899ad3d8c930e7c2a_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM7.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8c9fd29832f749f5afb450ee4b6e4788_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM6.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5222e7e89ba64e2392509a149b7061ce_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM11.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSHORT-KB8432-PURPLE-S', 'label' => 'Purple - S', 'ukuran' => 'S', 'warna' => 'Purple', 'harga' => 279000, 'stok' => 4, 'berat_gram' => 220, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-ultimate365-tour-cardigan-blue',
                'nama_produk' => 'ULTIMATE365 Tour Cardigan Blue Women',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cardigan women yang cocok untuk layering.',
                'harga_dasar' => 379000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/9ddcb9dfa0f148439fa5171781c97321_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90276d41ebd848538cf9baad8e4379f9_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c22cb25feb7f43aa951d0ae5cb02a145_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WCARD-JX6659-BLUE-S', 'label' => 'Blue - S', 'ukuran' => 'S', 'warna' => 'Blue', 'harga' => 379000, 'stok' => 5, 'berat_gram' => 450, 'image' => null],
                    ['sku' => 'MOVR-WCARD-KE7781-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 379000, 'stok' => 4, 'berat_gram' => 450, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-denim-jacket-blue',
                'nama_produk' => 'DENIM Jacket Blue Women',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Jaket denim klasik untuk women.',
                'harga_dasar' => 529000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WJACKET-KR5042-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 529000, 'stok' => 3, 'berat_gram' => 720, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-workout-essentials-feelready-3-stripes-tshirt-black',
                'nama_produk' => 'WORKOUT ESSENTIALS FEELREADY 3 STRIPES T-SHIRT Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Kaos training dengan aksen 3 stripes.',
                'harga_dasar' => 199000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1660a4daf855418991ea6f86bff4ed98_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/149c6cef4cdb419aadfc87d95247e1bc_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/bf43ea13429a481682e682cfaf971bcf_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MTEE-KA3486-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 199000, 'stok' => 10, 'berat_gram' => 205, 'image' => null],
                    ['sku' => 'MOVR-MTEE-KD0608-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 199000, 'stok' => 6, 'berat_gram' => 205, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7d82779d8c16498bb3b9361a317797e2_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Blue_KD0608_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'man-y3-brushed-terry-gfx-hoodie-black',
                'nama_produk' => 'Y-3 Brushed Terry GFX Hoodie Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie Y-3 brushed terry dengan gaya grafis.',
                'harga_dasar' => 649000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5b5a679972f4452c89b30347b62cf0e9_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4887db20479349f0a226cc2522de6f27_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/711c0d68cc32445c8d38ab0b50cca580_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KR2208-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 649000, 'stok' => 3, 'berat_gram' => 680, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-jacket-entire-studios-mid-layer-beige',
                'nama_produk' => 'adidas x entire studios Training Mid layer Jacket Beige Women',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Mid layer jacket women dengan tone beige.',
                'harga_dasar' => 559000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WJACKET-KD6091-BEIGE-M', 'label' => 'Beige - M', 'ukuran' => 'M', 'warna' => 'Beige', 'harga' => 559000, 'stok' => 4, 'berat_gram' => 700, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-tracksuit-song-for-the-mute-007-brown',
                'nama_produk' => 'SONG FOR THE MUTE 007 Track Top Brown Men',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top untuk set tracksuit pria.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACK-MEN-KS1339-BROWN-M', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                    ['sku' => 'MOVR-TRACK-MEN-KS1340-GREY-L', 'label' => 'Grey - L', 'ukuran' => 'L', 'warna' => 'Grey', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'man-adicolor-denim-firebird-track-top-blue',
                'nama_produk' => 'ADICOLOR Denim Firebird Track Top Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top denim firebird untuk gaya retro.',
                'harga_dasar' => 449000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf3deb85af984d44be1731fc748a8d8e_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/76be5741f7574db686940b7242c492bd_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM3_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/783538ac87c646fe985e9b08e36cf255_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KD1517-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 449000, 'stok' => 5, 'berat_gram' => 590, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-sst-loose-mesh-track-top-black',
                'nama_produk' => 'SST Loose Mesh Track Top Black Men',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top mesh pria untuk set olahraga.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7186bd540e274780b106fb151fd2c7dd_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8133dd62c37d4e64849ae336aa215628_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM3_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6f354a2bea214c20a8bacc3c29c53c85_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KE0115-BLACK-M2', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 459000, 'stok' => 5, 'berat_gram' => 560, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-tiro-shoebag-black',
                'nama_produk' => 'TIRO Shoebag Black Men',
                'kategori_slug' => 'man-accessories',
                'deskripsi' => 'Shoebag compact untuk pria.',
                'harga_dasar' => 159000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f2153fb7ba95457f8c96ccc5f98566b2_9366/TIRO_SHOEBAG_Black_JY7993_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f10c27392d24798ab251908d1b0d9fe_9366/TIRO_SHOEBAG_Black_JY7993_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f8286e3ca96497ca21a611ccacaec90_9366/TIRO_SHOEBAG_Black_JY7993_04_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-BAG-JY7993-BLACK-OS2', 'label' => 'Black - OS', 'ukuran' => 'OS', 'warna' => 'Black', 'harga' => 159000, 'stok' => 12, 'berat_gram' => 150, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-tiro-graphic-organizer-blue',
                'nama_produk' => 'adidas Tiro Graphic Organizer Blue Women',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Organizer bag untuk women.',
                'harga_dasar' => 179000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/57c6c3f819634d47babe2e59ba0199af_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/50d405de54dd460a8b5ad170a1dc0d22_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8510ec63c31f45779baf2bf9f50c4295_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_04_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-KE6849-BLUE-OS', 'label' => 'Blue - OS', 'ukuran' => 'OS', 'warna' => 'Blue', 'harga' => 179000, 'stok' => 10, 'berat_gram' => 160, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-unisex-aop-cart-golf-bag-black',
                'nama_produk' => 'UNISEX AOP CART GOLF BAG Black Women',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Cart golf bag berukuran besar.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/23cc0f9e36b7488593aeafa54772a5d0_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/51211e7b6bc647c29e0df29be16bd501_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_02_standard_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0813f420a1b6403ba4fea7db05386108_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-KA4948-BLACK-OS', 'label' => 'Black - OS', 'ukuran' => 'OS', 'warna' => 'Black', 'harga' => 499000, 'stok' => 3, 'berat_gram' => 1200, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-unisex-synthetic-leather-cart-bag-grey',
                'nama_produk' => 'Unisex Synthetic Leather Cart Bag Grey',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Cart bag sintetis untuk kebutuhan golf atau travel.',
                'harga_dasar' => 529000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3db6ed420b2840128a780a27091074e9_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2f7eba3f63ab4bb984d597581b349e62_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/11aab51e3c664f2ea7ba40158f5ba286_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-JZ4376-GREY-OS', 'label' => 'Grey - OS', 'ukuran' => 'OS', 'warna' => 'Grey', 'harga' => 529000, 'stok' => 3, 'berat_gram' => 1250, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-y3-brushed-terry-crew-sweatshirt-green',
                'nama_produk' => 'Y-3 Brushed Terry Crew Sweatshirt Green',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Crew sweatshirt Y-3 dengan varian brown.',
                'harga_dasar' => 589000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6a0eb89e6faf4d11a12bebb41b1e0705_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b0a2e6229a2f44698b1cc884776af7b7_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/861eaf99804e4e2eb96551b867915beb_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-SWEAT-KS5451-GREEN-M', 'label' => 'Green - M', 'ukuran' => 'M', 'warna' => 'Green', 'harga' => 589000, 'stok' => 4, 'berat_gram' => 640, 'image' => null],
                    ['sku' => 'MOVR-SWEAT-KS5450-BROWN-L', 'label' => 'Brown - L', 'ukuran' => 'L', 'warna' => 'Brown', 'harga' => 589000, 'stok' => 3, 'berat_gram' => 640, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ac3608c1483140099e103deaba4881f9_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Brown_KS5450_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-originals-all-over-cardigan-white',
                'nama_produk' => 'Originals All Over Cardigan White',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cardigan all over print untuk women.',
                'harga_dasar' => 389000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/80a02739a590432e84e9a2f26181b197_9366/Originals_All_Over_Cardigan_White_KE7781_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f73d6e55c6d9443886e10cbbe2834b48_9366/Originals_All_Over_Cardigan_White_KE7781_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WCARD-KE7781-WHITE-S', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 389000, 'stok' => 4, 'berat_gram' => 430, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-originals-cashmere-sweater-blue',
                'nama_produk' => 'Originals Cashmere Sweater Blue',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cashmere sweater lembut untuk women.',
                'harga_dasar' => 609000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66119cef3a4144e6a33d83b09b69b9ee_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1881674ca2034768a41a69081e826ff6_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM3_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8095bbe86e3b4775a0ba257828d3b688_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSWEATER-JY5291-BLUE-S', 'label' => 'Blue - S', 'ukuran' => 'S', 'warna' => 'Blue', 'harga' => 609000, 'stok' => 3, 'berat_gram' => 520, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-audi-revolut-f1-team-teamgeist-shorts-black',
                'nama_produk' => 'AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts motorsport dengan style teamgeist.',
                'harga_dasar' => 299000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dfb0c7b407b4ba7aab13caecf3c5f03_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/dc205a525f4c4eb1b173998380f3e7b4_faec/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM3_hover.tiff.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5bda9c8f2142440aa9794e4f557f1a70_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MSHORT-KQ8631-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 299000, 'stok' => 5, 'berat_gram' => 230, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-originals-all-over-cardigan-white-variant',
                'nama_produk' => 'Originals All Over Cardigan White Variant',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Duplicate seed to preserve alternate cardigans from the original list.',
                'harga_dasar' => 389000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/80a02739a590432e84e9a2f26181b197_9366/Originals_All_Over_Cardigan_White_KE7781_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f73d6e55c6d9443886e10cbbe2834b48_9366/Originals_All_Over_Cardigan_White_KE7781_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WCARD-KE7781-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 389000, 'stok' => 4, 'berat_gram' => 430, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-y3-ft-hoodie-black',
                'nama_produk' => 'Y-3 FT Hoodie Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie Y-3 FT warna black.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5925658e81f648e2a23cea0c923d89db_9366/Y-3_FT_Hoodie_Black_KA3112_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KA3112-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 459000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-y3-ft-hoodie-purple',
                'nama_produk' => 'Y-3 FT Hoodie Purple',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie Y-3 FT warna purple.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/249e959e48a74166ab965f7662b2352b_9366/Y-3_FT_Hoodie_Purple_KA3113_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KA3113-PURPLE-M', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 459000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-tracksuit-song-for-the-mute-007-grey',
                'nama_produk' => 'SONG FOR THE MUTE 007 Track Top Grey',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top grey untuk tracksuit pria.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACK-MEN-KS1340-GREY-M', 'label' => 'Grey - M', 'ukuran' => 'M', 'warna' => 'Grey', 'harga' => 499000, 'stok' => 4, 'berat_gram' => 620, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-tracksuit-adicolor-denim-firebird-blue',
                'nama_produk' => 'ADICOLOR Denim Firebird Track Top Blue Alt',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Track top denim firebird pria.',
                'harga_dasar' => 449000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf3deb85af984d44be1731fc748a8d8e_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM1.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-TRACKTOP-KD1517-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 449000, 'stok' => 4, 'berat_gram' => 590, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-summer-glow-tee-white-alt',
                'nama_produk' => 'ADIDAS Originals Summer Glow Advanced Three Stripes Tee White Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Tee women putih dengan varian black.',
                'harga_dasar' => 229000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ea8bc0b3a404cd09a62e6fe4447fe55_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WTEE-KY8126-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 229000, 'stok' => 7, 'berat_gram' => 200, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-adi365-running-2-in-1-shorts-purple-alt',
                'nama_produk' => 'Adi365 H.Koumori Running 2-In-1 Shorts Purple Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Running shorts 2-in-1 alternate seed.',
                'harga_dasar' => 279000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/324f65ca0c1b414899ad3d8c930e7c2a_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM7.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSHORT-KB8432-PURPLE-M', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 279000, 'stok' => 4, 'berat_gram' => 220, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-summer-glow-vintage-tee-pink-alt',
                'nama_produk' => 'ADIDAS Originals Summer Glow Vintage Tee Pink',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Vintage tee pink variant.',
                'harga_dasar' => 209000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a6218aab06be4995b7493c52d3d51702_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Pink_KY8130_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WTEE-KY8130-PINK-S', 'label' => 'Pink - S', 'ukuran' => 'S', 'warna' => 'Pink', 'harga' => 209000, 'stok' => 4, 'berat_gram' => 185, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-short-pink-brown-alt',
                'nama_produk' => 'ADIDAS Originals Summer Glow Shorts Pink Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Summer glow shorts with brown alt variant.',
                'harga_dasar' => 189000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8dcbbeac9a3847a989c1eb0c85e24cfe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_21_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSHORT-KY3169-PINK-M', 'label' => 'Pink - M', 'ukuran' => 'M', 'warna' => 'Pink', 'harga' => 189000, 'stok' => 8, 'berat_gram' => 220, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-audi-revolut-f1-team-geist-shorts-black',
                'nama_produk' => 'AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts motorsport dengan gaya teamgeist.',
                'harga_dasar' => 299000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dfb0c7b407b4ba7aab13caecf3c5f03_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/dc205a525f4c4eb1b173998380f3e7b4_faec/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM3_hover.tiff.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5bda9c8f2142440aa9794e4f557f1a70_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MSHORT-KQ8631-BLACK-M2', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 299000, 'stok' => 5, 'berat_gram' => 230, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-jacket-entire-studios-training-mid-layer-beige',
                'nama_produk' => 'adidas x entire studios Training Mid Layer Jacket Beige',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Mid layer jacket dengan tone beige untuk pria.',
                'harga_dasar' => 559000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MJACKET-KD6091-BEIGE-M', 'label' => 'Beige - M', 'ukuran' => 'M', 'warna' => 'Beige', 'harga' => 559000, 'stok' => 4, 'berat_gram' => 700, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-jacket-denim-blue',
                'nama_produk' => 'DENIM Jacket Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Jaket denim klasik warna blue untuk pria.',
                'harga_dasar' => 529000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MJACKET-KR5042-BLUE-M', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 529000, 'stok' => 3, 'berat_gram' => 720, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-shorts-print-seersucker-white',
                'nama_produk' => 'PRINTED SEERSUCKER Shorts White',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts seersucker ringan untuk cuaca panas.',
                'harga_dasar' => 179000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/638855057e3a40c5a7b76a02e9e87316_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/42b4d64fd4494ddcbb1dd63e04b981d0_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1c4afe3d416246ce9cdae8d6799f3d11_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_01_laydown.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MSHORT-KX1229-WHITE-M', 'label' => 'White - M', 'ukuran' => 'M', 'warna' => 'White', 'harga' => 179000, 'stok' => 10, 'berat_gram' => 210, 'image' => null],
                    ['sku' => 'MOVR-MSHORT-KX1228-BLUE-L', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 179000, 'stok' => 7, 'berat_gram' => 210, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aff1f1ee3ce248638621d3d4354ec0e1_9366/PRINTED_SEERSUCKER_SHORTS_Blue_KX1228_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'man-shorts-loose-engineered-black',
                'nama_produk' => '3-Stripes Loose Engineered Shorts Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Shorts casual loose fit untuk aktifitas harian.',
                'harga_dasar' => 199000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf5d11c2bae2456eaffea145fea84f1d_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/12096701657c4869857a380a0651ef7c_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc34a690deb84d1c87bd3f08c6bca6b2_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MSHORT-KE3594-BLACK-M', 'label' => 'Black - M', 'ukuran' => 'M', 'warna' => 'Black', 'harga' => 199000, 'stok' => 9, 'berat_gram' => 240, 'image' => null],
                    ['sku' => 'MOVR-MSHORT-KE3590-BLUE-L2', 'label' => 'Blue - L', 'ukuran' => 'L', 'warna' => 'Blue', 'harga' => 199000, 'stok' => 8, 'berat_gram' => 240, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0a17f40b0a994630ace3501901d83818_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Blue_KE3590_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'women-summer-glow-mesh-graphics-long-sleeve-white-alt',
                'nama_produk' => 'ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Long sleeve mesh graphics untuk gaya layer.',
                'harga_dasar' => 229000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f56c37da36644f78967a5fb52688eab_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/effed0d5433a4c7c98d5720f05a2763d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/55b8f11611a34127afd9345611396b96_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WLS-KY8138-WHITE-S2', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 229000, 'stok' => 6, 'berat_gram' => 195, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-originals-cashmere-sweater-blue-alt',
                'nama_produk' => 'Originals Cashmere Sweater Blue Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Cashmere sweater lembut untuk women.',
                'harga_dasar' => 609000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66119cef3a4144e6a33d83b09b69b9ee_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM1.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1881674ca2034768a41a69081e826ff6_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM3_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8095bbe86e3b4775a0ba257828d3b688_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM4.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WSWEATER-JY5291-BLUE-M2', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 609000, 'stok' => 3, 'berat_gram' => 520, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-hoodie-adi365-cheering-purple',
                'nama_produk' => 'adi365 Cheering Hoodie Purple Women',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Hoodie women dengan varian white.',
                'harga_dasar' => 449000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WHOODIE-KA0160-PURPLE-M', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 449000, 'stok' => 6, 'berat_gram' => 610, 'image' => null],
                    ['sku' => 'MOVR-WHOODIE-KA0331-WHITE-S2', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 449000, 'stok' => 5, 'berat_gram' => 610, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'man-jacket-zne-woven-bomber-white',
                'nama_produk' => 'ADIDAS Z.N.E. Woven Bomber White',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Bomber jacket ringan untuk style modern.',
                'harga_dasar' => 599000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5d17f749e3a545e69ab907a68bf769d4_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f40899fcc494ee59842d515937cec9f_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MJACKET-KD8507-WHITE-L', 'label' => 'White - L', 'ukuran' => 'L', 'warna' => 'White', 'harga' => 599000, 'stok' => 4, 'berat_gram' => 680, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-hoodie-jude-bellingham-black',
                'nama_produk' => 'Jude Bellingham Hoodie Black',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie signature dengan style sporty.',
                'harga_dasar' => 479000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66a0d7ee4ed841918af0dc928d28101a_9366/Jude_Bellingham_Hoodie_Black_KD6519_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dc03ac01a204f0182c16c76ce7df2b7_9366/Jude_Bellingham_Hoodie_Black_KD6519_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/86ebeef63aa84907812b66a1a49ed1f9_9366/Jude_Bellingham_Hoodie_Black_KD6519_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KD6519-BLACK-L', 'label' => 'Black - L', 'ukuran' => 'L', 'warna' => 'Black', 'harga' => 479000, 'stok' => 4, 'berat_gram' => 630, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-accessory-pet-shoulder-bag-carrier-brown',
                'nama_produk' => 'PET Shoulder Bag Carrier Brown',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Shoulder bag carrier untuk penggunaan harian.',
                'harga_dasar' => 199000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f11691d4081d407db0fdf5c3ea70976e_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/36b6324044ea46ff93da0abb8c9c3691_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_04_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/45d05df146d0487090bd897e252ec09b_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-KY8726-BROWN-OS', 'label' => 'Brown - OS', 'ukuran' => 'OS', 'warna' => 'Brown', 'harga' => 199000, 'stok' => 9, 'berat_gram' => 180, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-accessory-tiro-graphic-organizer-blue',
                'nama_produk' => 'adidas Tiro Graphic Organizer Blue',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Organizer bag untuk kebutuhan harian.',
                'harga_dasar' => 179000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/57c6c3f819634d47babe2e59ba0199af_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/50d405de54dd460a8b5ad170a1dc0d22_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8510ec63c31f45779baf2bf9f50c4295_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_04_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-KE6849-BLUE-OS2', 'label' => 'Blue - OS', 'ukuran' => 'OS', 'warna' => 'Blue', 'harga' => 179000, 'stok' => 10, 'berat_gram' => 160, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-accessory-unisex-aop-cart-golf-bag-black',
                'nama_produk' => 'UNISEX AOP Cart Golf Bag Black',
                'kategori_slug' => 'man-accessories',
                'deskripsi' => 'Cart golf bag berukuran besar.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/23cc0f9e36b7488593aeafa54772a5d0_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/51211e7b6bc647c29e0df29be16bd501_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_02_standard_hover.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0813f420a1b6403ba4fea7db05386108_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MBAG-KA4948-BLACK-OS', 'label' => 'Black - OS', 'ukuran' => 'OS', 'warna' => 'Black', 'harga' => 499000, 'stok' => 3, 'berat_gram' => 1200, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-hoodie-adi365-cheering-purple-alt',
                'nama_produk' => 'adi365 Cheering Hoodie Purple Alt',
                'kategori_slug' => 'women-clothing',
                'deskripsi' => 'Hoodie women tambahan dengan varian white.',
                'harga_dasar' => 449000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WHOODIE-KA0160-PURPLE-M2', 'label' => 'Purple - M', 'ukuran' => 'M', 'warna' => 'Purple', 'harga' => 449000, 'stok' => 6, 'berat_gram' => 610, 'image' => null],
                    ['sku' => 'MOVR-WHOODIE-KA0331-WHITE-S3', 'label' => 'White - S', 'ukuran' => 'S', 'warna' => 'White', 'harga' => 449000, 'stok' => 5, 'berat_gram' => 610, 'image' => 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg'],
                ],
            ],
            [
                'slug' => 'man-y3-ft-hoodie-brown-alt',
                'nama_produk' => 'Y-3 FT Hoodie Brown Alt',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie Y-3 FT warna brown dari daftar asli.',
                'harga_dasar' => 459000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/340e096532c244e684dc36f5f63ce6f0_9366/Y-3_FT_Hoodie_Brown_KS5430_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e7c17f69224450fad455869f70c947a_9366/Y-3_FT_Hoodie_Brown_KS5430_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/79418cbe6e974465a565af7e8fea7299_9366/Y-3_FT_Hoodie_Brown_KS5430_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KS5430-BROWN-M', 'label' => 'Brown - M', 'ukuran' => 'M', 'warna' => 'Brown', 'harga' => 459000, 'stok' => 6, 'berat_gram' => 620, 'image' => null],
                ],
            ],
            [
                'slug' => 'man-d4t-workout-full-zip-hoodie-blue',
                'nama_produk' => 'D4T Workout Full-Zip Hoodie Blue',
                'kategori_slug' => 'man-clothing',
                'deskripsi' => 'Hoodie full zip untuk training pria.',
                'harga_dasar' => 499000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c392460a8a1d4453a790fe9a3c0c838f_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_21_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aac9a8d12bb7438eb38c4a184915f90b_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_23_hover_model.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7e4b9ba2fdf847ce94827127192a983a_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_25_model.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-MHOODIE-KA4822-BLUE-M2', 'label' => 'Blue - M', 'ukuran' => 'M', 'warna' => 'Blue', 'harga' => 499000, 'stok' => 5, 'berat_gram' => 610, 'image' => null],
                ],
            ],
            [
                'slug' => 'women-unisex-synthetic-leather-cart-bag-grey',
                'nama_produk' => 'Unisex Synthetic Leather Cart Bag Grey',
                'kategori_slug' => 'women-accessories',
                'deskripsi' => 'Cart bag sintetis untuk kebutuhan golf atau travel.',
                'harga_dasar' => 529000,
                'images' => [
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3db6ed420b2840128a780a27091074e9_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_01_00_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2f7eba3f63ab4bb984d597581b349e62_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_02_standard.jpg',
                    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/11aab51e3c664f2ea7ba40158f5ba286_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_05_hover_standard.jpg',
                ],
                'variants' => [
                    ['sku' => 'MOVR-WBAG-JZ4376-GREY-OS2', 'label' => 'Grey - OS', 'ukuran' => 'OS', 'warna' => 'Grey', 'harga' => 529000, 'stok' => 3, 'berat_gram' => 1250, 'image' => null],
                ],
            ],
        ];

        $detailIdBySku = [];

        foreach ($products as $product) {
            $kategoriId = $kategoriMap[$product['kategori_slug']] ?? null;
            if (! $kategoriId) {
                continue;
            }

            $productRow = [
                'supplier_id' => $supplier->supplier_id,
                'kategori_id' => $kategoriId,
                'nama_produk' => $product['nama_produk'],
                'slug' => $product['slug'],
                'deskripsi' => $product['deskripsi'],
                'harga_dasar' => $product['harga_dasar'],
                'total_terjual' => 0,
                'rata_rating' => 0,
                'jumlah_ulasan' => 0,
                'is_active' => 1,
                'is_featured' => 0,
                'penyimpanan_waktu' => now(),
                'updated_at' => now(),
            ];

            $productId = DB::table('produk')->where('slug', $product['slug'])->value('produk_id');
            if ($productId) {
                DB::table('produk')->where('produk_id', $productId)->update($productRow);
            } else {
                $productId = DB::table('produk')->insertGetId($productRow);
            }

            DB::table('gambar_produk')->where('produk_id', $productId)->delete();
            foreach ($product['images'] as $index => $imageUrl) {
                DB::table('gambar_produk')->insert([
                    'produk_id' => $productId,
                    'url_gambar' => $imageUrl,
                    'alt_text' => $product['nama_produk'],
                    'urutan' => $index + 1,
                    'created_at' => now(),
                ]);
            }

            foreach ($product['variants'] as $variant) {
                $detailRow = [
                    'produk_id' => $productId,
                    'nama_produk' => $variant['label'],
                    'ukuran' => $variant['ukuran'],
                    'harga' => $variant['harga'],
                    'stok' => $variant['stok'],
                    'sku' => $variant['sku'],
                    'berat_gram' => $variant['berat_gram'],
                    'is_active' => 1,
                ];

                $detailId = DB::table('detail_produk')->where('sku', $variant['sku'])->value('detail_produk_id');
                if ($detailId) {
                    DB::table('detail_produk')->where('detail_produk_id', $detailId)->update($detailRow);
                } else {
                    $detailId = DB::table('detail_produk')->insertGetId($detailRow);
                }

                $detailIdBySku[$variant['sku']] = $detailId;

                if (Schema::hasTable('gambar_detail_produk')) {
                    DB::table('gambar_detail_produk')->where('detail_produk_id', $detailId)->delete();
                    if (! empty($variant['image'])) {
                        DB::table('gambar_detail_produk')->insert([
                            'detail_produk_id' => $detailId,
                            'url_gambar' => $variant['image'],
                            'alt_text' => $variant['label'],
                            'urutan' => 1,
                            'created_at' => now(),
                        ]);
                    }
                }
            }
        }

        $selectedItems = [
            ['sku' => 'MOVR-TSHIRT-KE7964-BEIGE-S', 'quantity' => 2],
            ['sku' => 'MOVR-POLO-KB1394-BLUE-M', 'quantity' => 1],
        ];

        $selectedDetailIds = [];
        $subtotal = 0;
        foreach ($selectedItems as $item) {
            $detailId = $detailIdBySku[$item['sku']] ?? DB::table('detail_produk')->where('sku', $item['sku'])->value('detail_produk_id');
            if (! $detailId) {
                continue;
            }

            $detail = DB::table('detail_produk')->where('detail_produk_id', $detailId)->first();
            if (! $detail) {
                continue;
            }

            $selectedDetailIds[] = [
                'detail_id' => $detailId,
                'quantity' => $item['quantity'],
                'price' => (float) $detail->harga,
            ];
            $subtotal += ((float) $detail->harga) * $item['quantity'];
        }

        $alamat = DB::table('alamat_pengguna')->where('pengguna_id', $buyer->pengguna_id)->orderByDesc('is_utama')->orderByDesc('alamat_id')->first();
        if (! $alamat) {
            $alamatId = DB::table('alamat_pengguna')->insertGetId([
                'pengguna_id' => $buyer->pengguna_id,
                'label' => 'Rumah',
                'nama_penerima' => $buyer->nama_pengguna,
                'no_telepon' => $buyer->no_telepon,
                'provinsi' => 'DKI Jakarta',
                'kota' => 'Jakarta Pusat',
                'kecamatan' => 'Menteng',
                'kelurahan' => 'Cikini',
                'kode_pos' => '10330',
                'alamat_lengkap' => 'Jl. Demo No. 1, Jakarta',
                'is_utama' => 1,
                'created_at' => now(),
            ]);
            $alamat = DB::table('alamat_pengguna')->where('alamat_id', $alamatId)->first();
        }

        DB::table('keranjang')->where('pengguna_id', $buyer->pengguna_id)->delete();
        foreach ($selectedDetailIds as $item) {
            DB::table('keranjang')->insert([
                'pengguna_id' => $buyer->pengguna_id,
                'detail_produk_id' => $item['detail_id'],
                'jumlah' => $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $ongkosKirim = 25000;
        $diskonVoucher = 0;
        $totalHarga = $subtotal + $ongkosKirim - $diskonVoucher;
        $kodeTransaksi = 'MOVR-DEMO-ORDER-001';

        $transaksiId = DB::table('transaksi')->where('kode_transaksi', $kodeTransaksi)->value('transaksi_id');
        $transaksiRow = [
            'pengguna_id' => $buyer->pengguna_id,
            'alamat_id' => $alamat->alamat_id,
            'ekspedisi_id' => $jneReg->ekspedisi_id,
            'voucher_id' => null,
            'kode_transaksi' => $kodeTransaksi,
            'subtotal' => $subtotal,
            'diskon_voucher' => $diskonVoucher,
            'ongkos_kirim' => $ongkosKirim,
            'total_harga' => $totalHarga,
            'status' => 'selesai',
            'catatan_buyer' => 'Demo checkout dari keranjang sampai pengiriman selesai.',
            'tanggal' => now()->subDays(2),
            'updated_at' => now(),
        ];

        if ($transaksiId) {
            DB::table('transaksi')->where('transaksi_id', $transaksiId)->update($transaksiRow);
        } else {
            $transaksiId = DB::table('transaksi')->insertGetId($transaksiRow);
        }

        DB::table('transaksi_detail')->where('transaksi_id', $transaksiId)->delete();
        foreach ($selectedDetailIds as $item) {
            $detail = DB::table('detail_produk')->where('detail_produk_id', $item['detail_id'])->first();
            if (! $detail) {
                continue;
            }

            DB::table('transaksi_detail')->insert([
                'transaksi_id' => $transaksiId,
                'detail_produk_id' => $detail->detail_produk_id,
                'nama_produk_snap' => $detail->nama_produk,
                'harga_snap' => $detail->harga,
                'ukuran_snap' => $detail->ukuran,
                'warna_snap' => $this->guessColorFromSku($detail->sku),
                'quantity' => $item['quantity'],
                'subtotal' => $detail->harga * $item['quantity'],
            ]);
        }

        DB::table('pembayaran')->where('transaksi_id', $transaksiId)->delete();
        DB::table('pembayaran')->insert([
            'transaksi_id' => $transaksiId,
            'metode_id' => $bca->metode_id,
            'jumlah_pembayaran' => $totalHarga,
            'status_pembayaran' => 'berhasil',
            'tanggal_pembayaran' => now()->subDays(2)->addHours(3),
            'bukti_pembayaran' => null,
            'ref_external' => 'PAY-MOVR-DEMO-001',
            'expired_at' => null,
            'created_at' => now()->subDays(2)->addHours(3),
            'updated_at' => now(),
        ]);

        DB::table('pesanan')->where('transaksi_id', $transaksiId)->delete();
        DB::table('pesanan')->insert([
            'transaksi_id' => $transaksiId,
            'ekspedisi_id' => $jneReg->ekspedisi_id,
            'no_resi' => 'JNE-MOVR-00123456789',
            'status_pesanan' => 'diterima',
            'alamat_pengiriman' => trim($alamat->label . ' - ' . $alamat->nama_penerima . ', ' . $alamat->alamat_lengkap . ', ' . $alamat->kota . ', ' . $alamat->provinsi . ' ' . ($alamat->kode_pos ?? '')),
            'foto_bukti' => null,
            'waktu_diambil' => now()->subDay(),
            'estimasi_tiba' => now()->addDays(2)->toDateString(),
            'created_at' => now()->subDays(2),
            'updated_at' => now(),
        ]);

        $pesananId = DB::table('pesanan')->where('transaksi_id', $transaksiId)->value('pesanan_id');
        if (! $pesananId) {
            return;
        }

        DB::table('tracking_log')->where('pesanan_id', $pesananId)->delete();
        $logs = [
            ['status' => 'Pesanan Dikonfirmasi', 'deskripsi' => 'Pesanan telah diterima admin dan divalidasi.', 'lokasi' => 'Gudang Utama', 'waktu_update' => now()->subDays(2)->addHours(1)],
            ['status' => 'Sedang Dikemas', 'deskripsi' => 'Item diambil dari gudang dan sedang dipacking.', 'lokasi' => 'Gudang Utama', 'waktu_update' => now()->subDays(2)->addHours(4)],
            ['status' => 'Siap Dikirim', 'deskripsi' => 'Paket sudah diserahkan ke tim ekspedisi.', 'lokasi' => 'Hub Asal', 'waktu_update' => now()->subDay()->addHours(2)],
            ['status' => 'Dalam Pengiriman', 'deskripsi' => 'Paket sedang berada di perjalanan menuju kota tujuan.', 'lokasi' => 'On Transit', 'waktu_update' => now()->subHours(18)],
            ['status' => 'Tiba di Tujuan', 'deskripsi' => 'Paket sudah tiba di hub tujuan dan siap diantar.', 'lokasi' => 'Hub Tujuan', 'waktu_update' => now()->subHours(5)],
            ['status' => 'Diterima', 'deskripsi' => 'Paket telah diterima oleh buyer.', 'lokasi' => 'Alamat Buyer', 'waktu_update' => now()->subHours(1)],
        ];

        foreach ($logs as $log) {
            DB::table('tracking_log')->insert([
                'pesanan_id' => $pesananId,
                'status' => $log['status'],
                'deskripsi' => $log['deskripsi'],
                'lokasi' => $log['lokasi'],
                'waktu_update' => $log['waktu_update'],
            ]);
        }
    }

    private function guessColorFromSku(string $sku): ?string
    {
        $colors = ['BEIGE', 'BLUE', 'WHITE', 'GREEN', 'PINK', 'BROWN', 'BLACK', 'PURPLE', 'GREY', 'DENIM'];

        foreach ($colors as $color) {
            if (str_contains(strtoupper($sku), $color)) {
                return $color;
            }
        }

        return null;
    }
}
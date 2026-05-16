-- Demo seed for MOVR
-- Jalankan di database kosong atau setelah truncate tabel terkait.
-- Flow: master data -> produk & varian -> keranjang -> checkout -> pembayaran -> tracking admin.

START TRANSACTION;

-- ------------------------------------------------------------
-- 1) MASTER DATA
-- ------------------------------------------------------------
INSERT INTO kategori (kategori_id, nama_kategori, slug, parent_id, level, urutan, banner_url, is_active, created_at)
VALUES
(1, 'Men T-Shirts', 'men-t-shirts', NULL, 1, 1, NULL, 1, NOW()),
(2, 'Men Hoodies', 'men-hoodies', NULL, 1, 2, NULL, 1, NOW()),
(3, 'Women T-Shirts', 'women-t-shirts', NULL, 1, 3, NULL, 1, NOW()),
(4, 'Men Shorts', 'men-shorts', NULL, 1, 4, NULL, 1, NOW()),
(5, 'Men Bags', 'men-bags', NULL, 1, 5, NULL, 1, NOW()),
(6, 'Women Sweatshirts', 'women-sweatshirts', NULL, 1, 6, NULL, 1, NOW()),
(7, 'Women Jackets', 'women-jackets', NULL, 1, 7, NULL, 1, NOW()),
(8, 'Accessories', 'accessories', NULL, 1, 8, NULL, 1, NOW());

INSERT INTO warna_produk (warna_id, nama_warna, kode_hex, is_active, created_at)
VALUES
(1, 'Beige', '#D8C3A5', 1, NOW()),
(2, 'Blue', '#2B6CB0', 1, NOW()),
(3, 'White', '#FFFFFF', 1, NOW()),
(4, 'Brown', '#8B5E3C', 1, NOW()),
(5, 'Black', '#111111', 1, NOW()),
(6, 'Purple', '#7E57C2', 1, NOW()),
(7, 'Pink', '#EC4899', 1, NOW()),
(8, 'Green', '#16A34A', 1, NOW()),
(9, 'Orange', '#F97316', 1, NOW());

INSERT INTO metode_pembayaran (metode_id, metode, jenis, logo_url, instruksi, is_active, created_at, updated_at)
VALUES
(1, 'BCA Virtual Account', 'transfer', NULL, 'Transfer ke virtual account yang ditampilkan saat checkout.', 1, NOW(), NOW()),
(2, 'QRIS', 'qris', NULL, 'Scan QR lalu lakukan pembayaran.', 1, NOW(), NOW()),
(3, 'COD', 'cod', NULL, 'Bayar saat barang diterima, jika tersedia.', 1, NOW(), NOW());

INSERT INTO ekspedisi (ekspedisi_id, nama_ekspedisi, jenis_layanan, estimasi_hari, logo_url, is_active, created_at, updated_at)
VALUES
(1, 'JNE', 'REG', '2-3 hari', NULL, 1, NOW(), NOW()),
(2, 'SiCepat', 'REG', '1-3 hari', NULL, 1, NOW(), NOW()),
(3, 'J&T', 'EZ', '2-4 hari', NULL, 1, NOW(), NOW());

-- ------------------------------------------------------------
-- 2) USERS / ADMIN / BUYER / SUPPLIER
-- ------------------------------------------------------------
INSERT INTO pengguna (
    pengguna_id, nama_pengguna, username, email, no_telepon, sandi,
    foto_profil, jenis_kelamin, tanggal_lahir, role, is_active, created_at, updated_at
)
VALUES
(1001, 'Master Administrator', 'admin-demo', 'admin.demo@movr.test', '081200000001', '$2y$10$UgDhHfDHRgTXPsMvcm.1Y.mknWuu/8cKLlgsNNRiUILwUKx8ZDIGm', NULL, NULL, NULL, 'admin', 1, NOW(), NOW()),
(1002, 'MOVR Supplier', 'supplier-demo', 'supplier.demo@movr.test', '081200000002', '$2y$10$bWCbPT01teOw8qB8VU9RduMID42MbYUc7nr146qJ4h702mxmezfie', NULL, NULL, NULL, 'supplier', 1, NOW(), NOW()),
(1003, 'Budi Santoso', 'buyer-demo', 'buyer.demo@movr.test', '081200000003', '$2y$10$.nH4f9SsIvhFVfX5bG9wHOcCvAmuU5L4Yd.A5Yzg3WdLSDRsh.m42', NULL, 'men', '1998-01-10', 'buyer', 1, NOW(), NOW());

INSERT INTO admin (admin_id, pengguna_id, created_at)
VALUES
(1, 1001, NOW());

INSERT INTO supplier (
    supplier_id, pengguna_id, nama_toko, nama_owner, alamat_toko, foto_toko, deskripsi_toko, is_verified, created_at, updated_at
)
VALUES
(1, 1002, 'MOVR Official Store', 'MOVR Admin', 'Jakarta, Indonesia', NULL, 'Toko demo untuk data produk sample.', 1, NOW(), NOW());

INSERT INTO buyer (buyer_id, pengguna_id, created_at)
VALUES
(1, 1003, NOW());

INSERT INTO alamat_pengguna (
    alamat_id, pengguna_id, label, nama_penerima, no_telepon, provinsi, kota, kecamatan, kelurahan, kode_pos, alamat_lengkap, is_utama, created_at
)
VALUES
(1, 1003, 'Rumah', 'Budi Santoso', '081200000003', 'DKI Jakarta', 'Jakarta Selatan', 'Setiabudi', 'Karet Semanggi', '12930', 'Jl. HR Rasuna Said No. 1, Jakarta Selatan', 1, NOW());

-- ------------------------------------------------------------
-- 3) PRODUCTS + IMAGES + VARIANTS
-- ------------------------------------------------------------
-- Product 1: MAN / Tshirt
INSERT INTO produk (
    produk_id, supplier_id, kategori_id, nama_produk, slug, deskripsi, spesifikasi,
    gender, tipe_olahraga, tags, harga_dasar, total_terjual, rata_rating, jumlah_ulasan,
    is_active, is_featured, penyimpanan_waktu, updated_at, status_publish, scheduled_at, stok_minimum
)
VALUES
(
    1001, 1, 1,
    '3-Stripes T-Shirt Beige',
    '3-stripes-t-shirt-beige',
    'Kaos casual 3 stripes warna beige dengan nuansa clean dan modern.',
    'Regular fit, cotton blend, breathable fabric',
    'men', 'Casual', '["tee","basic","casual"]',
    350000, 0, 0.00, 0,
    1, 1, NOW(), NOW(), 'publish', NULL, 5
),
(
    1002, 1, 2,
    'Y-3 FT Hoodie Brown',
    'y-3-ft-hoodie-brown',
    'Hoodie premium bernuansa sport-lifestyle.',
    'Relaxed fit, fleece material, warm fabric',
    'men', 'Lifestyle', '["hoodie","premium","lifestyle"]',
    899000, 0, 0.00, 0,
    1, 0, NOW(), NOW(), 'publish', NULL, 5
),
(
    1003, 1, 3,
    'ADIDAS ORIGINALS SUMMER GLOW ADVANCED THREE STRIPES TEE White',
    'adidas-originals-summer-glow-advanced-three-stripes-tee-white',
    'Tee wanita dengan desain summer glow yang ringan dan stylish.',
    'Slim fit, lightweight cotton, summer wear',
    'women', 'Lifestyle', '["tee","women","summer"]',
    329000, 0, 0.00, 0,
    1, 0, NOW(), NOW(), 'publish', NULL, 5
);

INSERT INTO gambar_produk (gambar_id, produk_id, url_gambar, alt_text, urutan)
VALUES
-- Product 1 images
(3001, 1001, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2dc7bcbdfbfa4cca8bc5f1ad34b9a571_9366/3-Stripes_T-Shirt_Beige_KE7964_21_model.jpg', '3-Stripes T-Shirt Beige', 0),
(3002, 1001, 'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/e4492c8c99cf4cd5ad7f8b5158a3960c_9366/3-Stripes_T-Shirt_Beige_KE7964_23_hover_model.jpg', '3-Stripes T-Shirt Beige', 1),
(3003, 1001, 'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/2bfda20fc36b41aeb7feec38a7e9d51b_9366/3-Stripes_T-Shirt_Beige_KE7964_25_model.jpg', '3-Stripes T-Shirt Beige', 2),
-- Product 2 images
(3004, 1002, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/340e096532c244e684dc36f5f63ce6f0_9366/Y-3_FT_Hoodie_Brown_KS5430_21_model.jpg', 'Y-3 FT Hoodie Brown', 0),
(3005, 1002, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e7c17f69224450fad455869f70c947a_9366/Y-3_FT_Hoodie_Brown_KS5430_23_hover_model.jpg', 'Y-3 FT Hoodie Brown', 1),
(3006, 1002, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/79418cbe6e974465a565af7e8fea7299_9366/Y-3_FT_Hoodie_Brown_KS5430_25_model.jpg', 'Y-3 FT Hoodie Brown', 2),
-- Product 3 images
(3007, 1003, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ea8bc0b3a404cd09a62e6fe4447fe55_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_21_model.jpg', 'Summer Glow Tee White', 0),
(3008, 1003, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/586c5bfb42b54688936457e524830e2d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_23_hover_model.jpg', 'Summer Glow Tee White', 1),
(3009, 1003, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6b7a1949f41d45b8830d4d6a39104110_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_25_model.jpg', 'Summer Glow Tee White', 2);

INSERT INTO detail_produk (
    detail_produk_id, produk_id, nama_produk, ukuran, harga, stok, sku, berat_gram, is_active
)
VALUES
(2001, 1001, '3-Stripes T-Shirt - Blue', 'M', 350000.00, 10, 'SKU-1001-BLU-M', 250, 1),
(2002, 1001, '3-Stripes T-Shirt - White', 'L', 360000.00, 8, 'SKU-1001-WHI-L', 250, 1),
(2003, 1002, 'Y-3 FT Hoodie - Black', 'M', 899000.00, 6, 'SKU-1002-BLK-M', 500, 1),
(2004, 1002, 'Y-3 FT Hoodie - Purple', 'L', 914000.00, 4, 'SKU-1002-PUR-L', 500, 1),
(2005, 1003, 'Summer Glow Tee - Black', 'S', 329000.00, 12, 'SKU-1003-BLK-S', 220, 1),
(2006, 1003, 'Summer Glow Tee - Pink', 'M', 339000.00, 5, 'SKU-1003-PNK-M', 220, 1);

INSERT INTO produk_supplier (produk_supplier_id, supplier_id, produk_id, harga_modal, catatan, created_at)
VALUES
(1, 1, 1001, 220000.00, 'Modal produk sample 1', NOW()),
(2, 1, 1002, 620000.00, 'Modal produk sample 2', NOW()),
(3, 1, 1003, 210000.00, 'Modal produk sample 3', NOW());

-- Variant image examples
INSERT INTO gambar_produk (gambar_id, produk_id, url_gambar, alt_text, urutan)
VALUES
(3010, 1001, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/608bd680d44649b38cc37c1d14d2a49b_9366/3-Stripes_T-Shirt_Blue_KE3536_21_model.jpg', '3-Stripes T-Shirt Blue', 10),
(3011, 1001, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/70a6980f8aab46d981b2caff95cc1ffb_9366/3-Stripes_T-Shirt_White_KE3537_21_model.jpg', '3-Stripes T-Shirt White', 11),
(3012, 1002, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5925658e81f648e2a23cea0c923d89db_9366/Y-3_FT_Hoodie_Black_KA3112_21_model.jpg', 'Y-3 FT Hoodie Black', 10),
(3013, 1002, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/249e959e48a74166ab965f7662b2352b_9366/Y-3_FT_Hoodie_Purple_KA3113_21_model.jpg', 'Y-3 FT Hoodie Purple', 11),
(3014, 1003, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f245aa400b8c4fa285d228e86194c1d5_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_Black_KY8127_21_model.jpg', 'Summer Glow Tee Black', 10),
(3015, 1003, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3eacf47e357e49bbbf00fe657f03898d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Orange_KY8139_21_model.jpg', 'Summer Glow Tee Pink/Orange Variant', 11);

-- ------------------------------------------------------------
-- 4) CART (BUYER ADD TO CART)
-- ------------------------------------------------------------
INSERT INTO keranjang (keranjang_id, pengguna_id, detail_produk_id, jumlah, created_at, updated_at)
VALUES
(4001, 1003, 2001, 1, NOW(), NOW()),
(4002, 1003, 2003, 1, NOW(), NOW());

-- ------------------------------------------------------------
-- 5) CHECKOUT -> TRANSAKSI + DETAIL + PEMBAYARAN
-- ------------------------------------------------------------
INSERT INTO transaksi (
    transaksi_id, pengguna_id, alamat_id, ekspedisi_id, voucher_id, kode_transaksi,
    subtotal, diskon_voucher, ongkos_kirim, total_harga, status, catatan_buyer,
    tanggal, updated_at
)
VALUES
(
    5001, 1003, 1, 1, NULL, 'TRX-20260516-0001',
    1249000.00, 0.00, 25000.00, 1274000.00, 'menunggu_pembayaran',
    'Mohon kirim pada jam kerja.', NOW(), NOW()
);

INSERT INTO transaksi_detail (
    detail_id, transaksi_id, detail_produk_id, nama_produk_snap, harga_snap,
    ukuran_snap, warna_snap, quantity, subtotal
)
VALUES
(6001, 5001, 2001, '3-Stripes T-Shirt - Blue', 350000.00, 'M', 'Blue', 1, 350000.00),
(6002, 5001, 2003, 'Y-3 FT Hoodie - Black', 899000.00, 'M', 'Black', 1, 899000.00);

INSERT INTO pembayaran (
    pembayaran_id, transaksi_id, metode_id, jumlah_pembayaran, status_pembayaran,
    tanggal_pembayaran, bukti_pembayaran, ref_external, expired_at, created_at, updated_at
)
VALUES
(7001, 5001, 2, 1274000.00, 'menunggu', NULL, NULL, 'QRIS-20260516-0001', DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW(), NOW());

-- Buyer checkout selesai, keranjang dikosongkan
DELETE FROM keranjang WHERE pengguna_id = 1003;

-- ------------------------------------------------------------
-- 6) ADMIN MEMPROSES PEMBAYARAN / ORDER
-- ------------------------------------------------------------
UPDATE pembayaran
SET status_pembayaran = 'berhasil',
    tanggal_pembayaran = NOW(),
    updated_at = NOW()
WHERE pembayaran_id = 7001;

UPDATE transaksi
SET status = 'pembayaran_dikonfirmasi',
    updated_at = NOW()
WHERE transaksi_id = 5001;

-- Kurangi stok dan naikkan total terjual
UPDATE detail_produk
SET stok = stok - 1
WHERE detail_produk_id IN (2001, 2003);

UPDATE produk
SET total_terjual = total_terjual + 1,
    updated_at = NOW()
WHERE produk_id IN (1001, 1002);

INSERT INTO pesanan (
    pesanan_id, transaksi_id, ekspedisi_id, no_resi, status_pesanan,
    alamat_pengiriman, foto_bukti, waktu_diambil, estimasi_tiba, created_at, updated_at
)
VALUES
(
    8001, 5001, 1, 'JNE-TRX-20260516-0001', 'dikemas',
    'Budi Santoso, Jl. HR Rasuna Said No. 1, Jakarta Selatan, DKI Jakarta, 12930',
    NULL, NOW(), DATE_ADD(CURDATE(), INTERVAL 4 DAY), NOW(), NOW()
);

INSERT INTO tracking_log (log_id, pesanan_id, status, deskripsi, lokasi, waktu_update)
VALUES
(9001, 8001, 'menunggu_konfirmasi', 'Pesanan masuk dan menunggu verifikasi admin.', 'Warehouse MOVR', NOW()),
(9002, 8001, 'dikemas', 'Pesanan sedang dikemas oleh admin.', 'Warehouse MOVR', DATE_ADD(NOW(), INTERVAL 15 MINUTE)),
(9003, 8001, 'diserahkan_ke_kurir', 'Paket diserahkan ke pihak ekspedisi.', 'Hub JNE', DATE_ADD(NOW(), INTERVAL 2 HOUR)),
(9004, 8001, 'dalam_pengiriman', 'Paket sedang diantar ke alamat tujuan.', 'On Delivery', DATE_ADD(NOW(), INTERVAL 1 DAY)),
(9005, 8001, 'diterima', 'Paket sudah diterima oleh buyer.', 'Jakarta Selatan', DATE_ADD(NOW(), INTERVAL 4 DAY));

UPDATE transaksi
SET status = 'selesai',
    updated_at = NOW()
WHERE transaksi_id = 5001;

UPDATE pesanan
SET status_pesanan = 'diterima',
    waktu_diambil = NOW(),
    updated_at = NOW()
WHERE pesanan_id = 8001;

COMMIT;

-- Catatan:
-- detail_produk di schema proyek ini belum punya warna_id.
-- Jadi varian warna disimpan lewat nama produk snapshot, SKU, dan gambar produk.

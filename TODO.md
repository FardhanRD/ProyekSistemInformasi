- [ ] Tambahkan pengiriman/status di halaman Pesanan Saya (`resources/views/buyer/order/detail.blade.php`) berdasarkan data tracking log terbaru (atau `status_pesanan` terakhir).


- [ ] Pastikan `OrderController@show` memuat data tracking (`trackingLogs`) dan/atau status pengiriman agar view punya datanya.
- [ ] Tambahkan “Total Produk” di halaman Lacak Pesanan (`resources/views/buyer/tracking/index.blade.php`) untuk menampilkan total item (SUM jumlah), jumlah detail (count), dan total harga (tetap).
- [ ] Samakan status pengiriman yang ditampilkan antara “Pesanan Saya” dan “Lacak Pengiriman”.
- [ ] Jalankan test/build (minimal `php artisan route:cache` bila perlu) dan cek error blade rendering.


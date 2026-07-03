<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\Transaksi;
use App\Models\Pembayaran;

$t = Transaksi::where('kode_transaksi', 'INV-TEST-REJECT-01')->first();
if (!$t) {
    $t = Transaksi::create([
        'pengguna_id' => 2, // Buyer ID
        'alamat_id' => 1,
        'ekspedisi_id' => 1,
        'kode_transaksi' => 'INV-TEST-REJECT-01',
        'subtotal' => 100000,
        'diskon_voucher' => 0,
        'ongkos_kirim' => 10000,
        'total_harga' => 110000,
        'status' => 'menunggu_pembayaran',
        'catatan_buyer' => 'Test reject payment',
        'tanggal' => now(),
    ]);
} else {
    $t->update(['status' => 'menunggu_pembayaran']);
}

$p = Pembayaran::where('transaksi_id', $t->transaksi_id)->first();
if (!$p) {
    Pembayaran::create([
        'transaksi_id' => $t->transaksi_id,
        'metode_id' => 1,
        'jumlah_pembayaran' => 110000,
        'status_pembayaran' => 'menunggu_konfirmasi',
        'bukti_pembayaran' => 'bukti_pembayaran/dummy.jpg',
        'expired_at' => now()->addDays(1),
    ]);
} else {
    $p->update([
        'status_pembayaran' => 'menunggu_konfirmasi',
        'bukti_pembayaran' => 'bukti_pembayaran/dummy.jpg',
    ]);
}

echo "Successfully seeded pending payment for INV-TEST-REJECT-01.\n";

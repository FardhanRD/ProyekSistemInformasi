<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    // Bersihkan notifikasi lama agar bersih
    DB::table('notifikasi')->where('pengguna_id', $admin->pengguna_id)->delete();

    // Tambahkan 1 notifikasi baru yang belum dibaca
    DB::table('notifikasi')->insert([
        'pengguna_id' => $admin->pengguna_id,
        'judul' => 'Pesanan Baru Masuk',
        'pesan' => 'Ada pesanan baru #MOVR-1002 yang perlu Anda proses.',
        'jenis' => 'order',
        'is_read' => false,
        'created_at' => now(),
    ]);
    echo "Successfully seeded 1 unread notification for Admin.\n";
} else {
    echo "Admin user not found.\n";
}

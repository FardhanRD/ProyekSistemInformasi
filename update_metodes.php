<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Updating metode pembayaran...\n";
    
    $updates = [
        'BCA' => 'BCA Virtual Account',
        'Mandiri' => 'Mandiri Virtual Account',
        'BNI' => 'BNI Virtual Account',
        'BRI' => 'BRI Virtual Account'
    ];
    
    foreach ($updates as $old => $new) {
        $count = DB::table('metode_pembayaran')
            ->where('metode', $old)
            ->update([
                'metode' => $new,
                'instruksi' => "Transfer ke nomor VA {$old} yang tertera. Pembayaran otomatis terkonfirmasi."
            ]);
        echo "✓ Updated {$count} record(s) for {$old}\n";
    }
    
    echo "\n\nVerifying updates:\n";
    $metodes = DB::table('metode_pembayaran')->where('jenis', 'transfer')->get();
    foreach ($metodes as $m) {
        echo "Metode: {$m->metode} | Jenis: {$m->jenis}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

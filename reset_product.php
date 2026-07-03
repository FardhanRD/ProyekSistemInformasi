<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produk;
use Illuminate\Support\Facades\DB;

$name = 'LAICA COURT PLEATS+skirt';
$produk = Produk::where('nama_produk', $name)->first();

if ($produk) {
    DB::beginTransaction();
    try {
        // Delete details
        $detailIds = $produk->details()->pluck('detail_produk_id');
        
        // Delete stock movements
        DB::table('stock_movements')->whereIn('detail_produk_id', $detailIds)->delete();
        
        // Delete transaction details if any (optional, usually not needed for fresh test runs)
        DB::table('transaksi_detail')->whereIn('detail_produk_id', $detailIds)->delete();
        
        // Delete product supplier relation
        DB::table('produk_supplier')->where('produk_id', $produk->produk_id)->delete();
        
        // Delete images
        $produk->images()->delete();
        
        // Delete details
        $produk->details()->delete();
        
        // Delete product
        $produk->delete();
        
        DB::commit();
        echo "Successfully deleted product '{$name}' and all its variants/images from database.\n";
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Product '{$name}' does not exist in the database. Ready for test run!\n";
}

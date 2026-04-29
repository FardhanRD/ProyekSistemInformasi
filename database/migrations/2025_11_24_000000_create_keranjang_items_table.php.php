<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('keranjang_items')) {
            return;
        }

        Schema::create('keranjang_items', function (Blueprint $table) {
            $table->id();
            // Tetap gunakan pembeli_id tapi hubungkan ke users
            $table->foreignId('pembeli_id')->constrained('users')->onDelete('cascade');
            
            // PERBAIKAN: Hubungkan ke tabel 'products' (bukan 'produk')
            $table->foreignId('produk_id')->constrained('products')->onDelete('cascade');
            
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_saat_ini', 12, 0); // Sesuaikan presisi harga
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang_items');
    }
};
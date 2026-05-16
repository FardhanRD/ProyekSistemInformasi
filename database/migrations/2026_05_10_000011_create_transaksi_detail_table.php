<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaksi_detail')) {
            return;
        }

        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('detail_produk_id');
            $table->string('nama_produk_snap', 255);
            $table->decimal('harga_snap', 15, 2);
            $table->string('ukuran_snap', 20)->nullable();
            $table->string('warna_snap', 80)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('subtotal', 15, 2);

            $table->foreign('transaksi_id')->references('transaksi_id')->on('transaksi')->onDelete('cascade');
            $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};

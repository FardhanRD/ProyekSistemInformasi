<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanan')) {
            return;
        }

        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('pesanan_id');
            $table->unsignedBigInteger('transaksi_id')->unique();
            $table->unsignedBigInteger('ekspedisi_id')->nullable();
            $table->foreign('ekspedisi_id')->references('ekspedisi_id')->on('ekspedisi')->nullOnDelete();
            $table->string('no_resi', 100)->nullable();
            $table->enum('status_pesanan', ['menunggu_konfirmasi','dikemas','siap_kirim','diserahkan_ke_kurir','dalam_pengiriman','tiba_di_tujuan','diterima','bermasalah'])->default('menunggu_konfirmasi');
            $table->text('alamat_pengiriman');
            $table->string('foto_bukti', 500)->nullable();
            $table->dateTime('waktu_diambil')->nullable();
            $table->date('estimasi_tiba')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

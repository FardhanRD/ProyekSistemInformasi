<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pembayaran')) {
            return;
        }

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('pembayaran_id');
            $table->unsignedBigInteger('transaksi_id')->unique();
            $table->unsignedBigInteger('metode_id');
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->enum('status_pembayaran', ['menunggu','menunggu_konfirmasi','berhasil','gagal','expired','refund'])->default('menunggu');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran', 500)->nullable();
            $table->string('ref_external', 200)->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->foreign('transaksi_id')->references('transaksi_id')->on('transaksi')->onDelete('cascade');
            $table->foreign('metode_id')->references('metode_id')->on('metode_pembayaran')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};

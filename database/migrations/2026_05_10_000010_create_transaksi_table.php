<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaksi')) {
            return;
        }

        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->foreignId('pengguna_id')->constrained('pengguna', 'pengguna_id')->onDelete('restrict');
            $table->unsignedBigInteger('alamat_id');
            $table->unsignedBigInteger('ekspedisi_id')->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->string('kode_transaksi', 50)->unique();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_voucher', 15, 2)->default(0);
            $table->decimal('ongkos_kirim', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->enum('status', ['menunggu_pembayaran','pembayaran_dikonfirmasi','diproses','dikirim','selesai','dibatalkan','refund'])->default('menunggu_pembayaran');
            $table->text('catatan_buyer')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->foreign('alamat_id')->references('alamat_id')->on('alamat_pengguna')->onDelete('restrict');
            $table->foreign('ekspedisi_id')->references('ekspedisi_id')->on('ekspedisi')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

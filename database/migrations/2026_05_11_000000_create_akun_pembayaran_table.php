<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('akun_pembayaran', function (Blueprint $table) {
            $table->id('akun_pembayaran_id');
            $table->unsignedBigInteger('pengguna_id');
            $table->unsignedBigInteger('metode_id');
            $table->string('nomor_akun', 255);
            $table->string('nama_akun', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pengguna_id')->references('pengguna_id')->on('pengguna')->onDelete('cascade');
            $table->foreign('metode_id')->references('metode_id')->on('metode_pembayaran')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_pembayaran');
    }
};
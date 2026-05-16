<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('keranjang')) {
            return;
        }

        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('keranjang_id');
            $table->foreignId('pengguna_id')->constrained('pengguna', 'pengguna_id')->onDelete('cascade');
            $table->unsignedBigInteger('detail_produk_id');
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['pengguna_id', 'detail_produk_id']);
            $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};

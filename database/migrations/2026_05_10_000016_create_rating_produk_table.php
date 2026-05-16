<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rating_produk')) {
            return;
        }

        Schema::create('rating_produk', function (Blueprint $table) {
            $table->id('rating_id');
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('buyer_id');
            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
            $table->foreign('buyer_id')->references('buyer_id')->on('buyer')->onDelete('cascade');
            $table->unsignedBigInteger('transaksi_id')->nullable();
            $table->unsignedTinyInteger('bintang');
            $table->string('judul_ulasan', 200)->nullable();
            $table->text('isi_ulasan')->nullable();
            $table->json('foto_ulasan')->nullable();
            $table->tinyInteger('is_verified')->default(1);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['produk_id', 'buyer_id', 'transaksi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_produk');
    }
};

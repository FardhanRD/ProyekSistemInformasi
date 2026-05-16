<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('detail_produk')) {
            return;
        }

        Schema::create('detail_produk', function (Blueprint $table) {
            $table->id('detail_produk_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('nama_produk')->nullable();
            $table->string('ukuran', 20)->nullable();
            $table->decimal('harga', 15, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->string('sku', 100)->nullable()->unique();
            $table->unsignedInteger('berat_gram')->default(0);
            $table->tinyInteger('is_active')->default(1);

            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_produk');
    }
};

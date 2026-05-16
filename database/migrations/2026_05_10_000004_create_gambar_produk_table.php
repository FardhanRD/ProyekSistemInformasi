<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gambar_produk')) {
            return;
        }

        Schema::create('gambar_produk', function (Blueprint $table) {
            $table->id('gambar_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('url_gambar', 500);
            $table->string('alt_text', 200)->nullable();
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_produk');
    }
};

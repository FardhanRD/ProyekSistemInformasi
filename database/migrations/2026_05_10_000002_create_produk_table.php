<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk')) {
            return;
        }

        Schema::create('produk', function (Blueprint $table) {
            $table->id('produk_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('nama_produk', 255);
            $table->string('slug', 300)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_dasar', 15, 2)->default(0);
            $table->unsignedInteger('total_terjual')->default(0);
            $table->decimal('rata_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('jumlah_ulasan')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_featured')->default(0);
            $table->timestamp('penyimpanan_waktu')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->foreign('kategori_id')->references('kategori_id')->on('kategori')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};

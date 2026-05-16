<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_movement')) {
            return;
        }

        Schema::create('stock_movement', function (Blueprint $table) {
            $table->id('movement_id');
            $table->unsignedBigInteger('detail_produk_id');
            $table->enum('jenis', ['in','out','adjustment']);
            $table->integer('qty')->comment('Positif = masuk, negatif = keluar');
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            $table->string('referensi', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable()->comment('admin_id atau sistem');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->onDelete('cascade');
            $table->index('detail_produk_id', 'idx_sm_detail');
            $table->index('jenis', 'idx_sm_jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('warna_produk')) {
            return;
        }

        Schema::create('warna_produk', function (Blueprint $table) {
            $table->id('warna_id');
            $table->string('nama_warna', 50);
            $table->string('kode_hex', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warna_produk');
    }
};

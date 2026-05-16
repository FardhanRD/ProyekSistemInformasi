<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('supplier')) {
            return;
        }

        Schema::create('supplier', function (Blueprint $table) {
            $table->id('supplier_id');
            $table->foreignId('pengguna_id')->unique()->constrained('pengguna', 'pengguna_id')->cascadeOnDelete();
            $table->string('nama_toko', 150);
            $table->string('nama_owner', 100);
            $table->text('alamat_toko')->nullable();
            $table->string('foto_toko', 500)->nullable();
            $table->text('deskripsi_toko')->nullable();
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};

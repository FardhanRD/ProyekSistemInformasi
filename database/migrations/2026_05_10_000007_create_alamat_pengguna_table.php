<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alamat_pengguna')) {
            return;
        }

        Schema::create('alamat_pengguna', function (Blueprint $table) {
            $table->id('alamat_id');
            $table->foreignId('pengguna_id')->constrained('pengguna', 'pengguna_id')->onDelete('cascade');
            $table->string('label', 50)->default('Rumah');
            $table->string('nama_penerima', 100);
            $table->string('no_telepon', 20);
            $table->string('provinsi', 100);
            $table->string('kota', 100);
            $table->string('kecamatan', 100);
            $table->string('kelurahan', 100);
            $table->string('kode_pos', 10)->nullable();
            $table->text('alamat_lengkap');
            $table->tinyInteger('is_utama')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamat_pengguna');
    }
};

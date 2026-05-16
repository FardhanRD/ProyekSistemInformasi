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
        if (Schema::hasTable('keranjang') && !Schema::hasColumn('keranjang', 'pengguna_id')) {
            Schema::table('keranjang', function (Blueprint $table) {
                // Tambahkan kolom pengguna_id
                $table->unsignedBigInteger('pengguna_id')->after('keranjang_id'); // Sesuaikan posisi jika perlu

                // Tambahkan foreign key constraint
                // Pastikan tabel 'pengguna' sudah ada dan memiliki primary key 'pengguna_id'
                $table->foreign('pengguna_id')
                      ->references('pengguna_id')
                      ->on('pengguna')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('keranjang') && Schema::hasColumn('keranjang', 'pengguna_id')) {
            Schema::table('keranjang', function (Blueprint $table) {
                $table->dropForeign(['pengguna_id']);
                $table->dropColumn('pengguna_id');
            });
        }
    }
};
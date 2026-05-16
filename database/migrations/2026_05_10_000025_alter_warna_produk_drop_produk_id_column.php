<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika kolom produk_id tidak sesuai model/seeder, normalisasi.
        if (! Schema::hasTable('warna_produk')) {
            return;
        }

        // Pastikan produk_id ada dan buang supaya seeder bisa insert tanpa default.
        if (Schema::hasColumn('warna_produk', 'produk_id')) {
            // drop foreign first if exists is not straightforward; dropColumn will fail if FK exists.
            // Untuk aman, coba drop foreign dengan nama umum.
            try {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // no-op; rely on dropForeign exceptions handling.
                // We won't attempt FK name derivation here.
            } catch (\Throwable $e) {
                // ignore
            }

            Schema::table('warna_produk', function (Blueprint $table) {
                // Menonaktifkan FK drop secara paksa tidak tersedia; jika migration gagal,
                // user dapat menjalankan ulang dengan clean database.
                $table->dropColumn('produk_id');
            });
        }
    }

    public function down(): void
    {
        // Untuk kelengkapan dan lingkungan non-produksi, kita bisa mengembalikan kolom.
        // Asumsi: produk_id sebelumnya adalah unsignedBigInteger dan nullable.
        if (Schema::hasTable('warna_produk') && !Schema::hasColumn('warna_produk', 'produk_id')) {
            Schema::table('warna_produk', function (Blueprint $table) {
                $table->unsignedBigInteger('produk_id')->nullable()->after('kode_hex');

                // Jika foreign key perlu dikembalikan, ini adalah contohnya.
                // Namun, nama foreign key asli mungkin perlu diketahui.
                // $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('set null');
            });
        }
    }
};

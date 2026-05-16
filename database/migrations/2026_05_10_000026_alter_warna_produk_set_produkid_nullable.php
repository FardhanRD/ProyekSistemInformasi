<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('warna_produk')) {
            return;
        }

        // Jika produk_id ada tapi tidak nullable (sehingga seeder gagal ketika insert tanpa produk_id),
        // ubah agar nullable.
        if (Schema::hasColumn('warna_produk', 'produk_id')) {
            Schema::table('warna_produk', function (Blueprint $table) {
                // Laravel tidak bisa mengubah nullability tanpa doctrine jika kolom ada.
                // Tapi di banyak setup, modify melalui `change()` bekerja.
                if (method_exists($table, 'integer')) {
                    // no-op
                }
                $table->unsignedBigInteger('produk_id')->nullable()->change();
            });
        }

        // Pastikan kolom is_active ada
        if (! Schema::hasColumn('warna_produk', 'is_active')) {
            Schema::table('warna_produk', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('kode_hex');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('warna_produk')) {
            return;
        }

        if (Schema::hasColumn('warna_produk', 'produk_id')) {
            Schema::table('warna_produk', function (Blueprint $table) {
                $table->unsignedBigInteger('produk_id')->nullable(false)->change();
            });
        }
    }
};


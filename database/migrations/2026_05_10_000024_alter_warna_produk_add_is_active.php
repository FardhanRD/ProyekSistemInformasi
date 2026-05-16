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

        if (Schema::hasColumn('warna_produk', 'is_active')) {
            Schema::table('warna_produk', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};


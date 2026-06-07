<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_produk', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_produk', 'warna_id')) {
                $table->unsignedBigInteger('warna_id')->nullable()->after('produk_id');
                $table->foreign('warna_id')->references('warna_id')->on('warna_produk')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_produk', function (Blueprint $table) {
            if (Schema::hasColumn('detail_produk', 'warna_id')) {
                $table->dropForeign(['warna_id']);
                $table->dropColumn('warna_id');
            }
        });
    }
};

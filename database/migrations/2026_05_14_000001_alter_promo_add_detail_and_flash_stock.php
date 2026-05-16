<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('promo')) {
            return;
        }

        Schema::table('promo', function (Blueprint $table) {
            if (!Schema::hasColumn('promo', 'detail_produk_id')) {
                $table->unsignedBigInteger('detail_produk_id')->nullable()->after('produk_id');
                $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->nullOnDelete();
            }

            if (!Schema::hasColumn('promo', 'stok_flash_sale')) {
                $table->unsignedInteger('stok_flash_sale')->nullable()->after('nominal_diskon');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('promo')) {
            return;
        }

        Schema::table('promo', function (Blueprint $table) {
            if (Schema::hasColumn('promo', 'stok_flash_sale')) {
                $table->dropColumn('stok_flash_sale');
            }

            if (Schema::hasColumn('promo', 'detail_produk_id')) {
                $table->dropForeign(['detail_produk_id']);
                $table->dropColumn('detail_produk_id');
            }
        });
    }
};

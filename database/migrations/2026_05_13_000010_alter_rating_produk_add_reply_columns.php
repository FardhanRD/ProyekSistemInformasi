<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rating_produk') && !Schema::hasColumn('rating_produk', 'balasan')) {
            Schema::table('rating_produk', function (Blueprint $table) {
                $table->text('balasan')->nullable()->after('helpful_count');
                $table->unsignedBigInteger('balas_oleh')->nullable()->after('balasan');
                $table->dateTime('balas_tanggal')->nullable()->after('balas_oleh');
                
                $table->foreign('balas_oleh')->references('admin_id')->on('admin')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rating_produk')) {
            Schema::table('rating_produk', function (Blueprint $table) {
                $table->dropForeignIdFor('Admin', 'balas_oleh');
                $table->dropColumn(['balasan', 'balas_oleh', 'balas_tanggal']);
            });
        }
    }
};

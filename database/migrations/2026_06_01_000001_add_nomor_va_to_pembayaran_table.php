<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran')) {
            return;
        }

        if (! Schema::hasColumn('pembayaran', 'nomor_va')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->string('nomor_va', 64)->nullable()->after('expired_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pembayaran') && Schema::hasColumn('pembayaran', 'nomor_va')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->dropColumn('nomor_va');
            });
        }
    }
};

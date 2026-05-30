<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rating_toko')) {
            return;
        }

        Schema::table('rating_toko', function (Blueprint $table) {
            if (! Schema::hasColumn('rating_toko', 'pelayanan')) {
                $table->unsignedTinyInteger('pelayanan')->nullable()->after('buyer_id');
            }

            if (! Schema::hasColumn('rating_toko', 'aplikasi')) {
                $table->unsignedTinyInteger('aplikasi')->nullable()->after('pelayanan');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('rating_toko')) {
            return;
        }

        Schema::table('rating_toko', function (Blueprint $table) {
            if (Schema::hasColumn('rating_toko', 'aplikasi')) {
                $table->dropColumn('aplikasi');
            }

            if (Schema::hasColumn('rating_toko', 'pelayanan')) {
                $table->dropColumn('pelayanan');
            }
        });
    }
};
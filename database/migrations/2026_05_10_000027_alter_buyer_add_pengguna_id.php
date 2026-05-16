<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('buyer') || ! Schema::hasTable('pengguna')) {
            return;
        }

        if (Schema::hasColumn('buyer', 'pengguna_id')) {
            return;
        }

        Schema::table('buyer', function (Blueprint $table) {
            $table->unsignedBigInteger('pengguna_id')->nullable()->after('buyer_id');

            $table->foreign('pengguna_id')
                ->references('pengguna_id')
                ->on('pengguna')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('buyer') || !Schema::hasColumn('buyer', 'pengguna_id')) {
            return;
        }

        Schema::table('buyer', function (Blueprint $table) {
            // Drop FK if exists (Laravel will infer name in most cases, but FK name can vary)
            $table->dropForeign(['pengguna_id']);
            $table->dropColumn('pengguna_id');
        });
    }
};


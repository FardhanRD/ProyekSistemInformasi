<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'alamat_pengguna' => 'cascade',
            'keranjang' => 'cascade',
            'wishlist' => 'cascade',
            'transaksi' => 'restrict',
            'supplier' => 'cascade',
        ];

        foreach ($tables as $tableName => $onDelete) {
            if (! Schema::hasTable($tableName) || ! Schema::hasTable('pengguna')) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'pengguna_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $onDelete) {
                    $table->unsignedBigInteger('pengguna_id')->nullable()->after('id');
                    $table->foreign('pengguna_id')->references('pengguna_id')->on('pengguna')->onDelete($onDelete);
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'alamat_pengguna',
            'keranjang',
            'wishlist',
            'transaksi',
            'supplier',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'pengguna_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['pengguna_id']);
                $table->dropColumn('pengguna_id');
            });
        }
    }
};


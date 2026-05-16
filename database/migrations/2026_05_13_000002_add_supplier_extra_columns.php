<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier', 'kategori_supplier')) {
                $table->string('kategori_supplier', 100)->nullable()->after('nama_toko');
            }
            if (!Schema::hasColumn('supplier', 'no_telepon')) {
                $table->string('no_telepon', 20)->nullable()->after('kategori_supplier');
            }
            if (!Schema::hasColumn('supplier', 'email')) {
                $table->string('email', 150)->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('supplier', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('alamat_toko');
            }
            if (!Schema::hasColumn('supplier', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('supplier', 'global_rank')) {
                $table->unsignedInteger('global_rank')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('supplier', 'total_orders')) {
                $table->unsignedInteger('total_orders')->default(0)->after('global_rank');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $cols = ['kategori_supplier','no_telepon','email','latitude','longitude','global_rank','total_orders'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('supplier', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            if (!Schema::hasColumn('produk', 'spesifikasi')) {
                $table->text('spesifikasi')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('produk', 'gender')) {
                $table->enum('gender', ['men','women','unisex','kids'])->default('unisex')->after('spesifikasi');
            }
            if (!Schema::hasColumn('produk', 'tipe_olahraga')) {
                $table->string('tipe_olahraga', 80)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('produk', 'tags')) {
                $table->json('tags')->nullable()->after('tipe_olahraga');
            }
            if (!Schema::hasColumn('produk', 'status_publish')) {
                $table->enum('status_publish', ['publish','draft','scheduled'])->default('draft')->after('tags');
            }
            if (!Schema::hasColumn('produk', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->after('status_publish');
            }
            if (!Schema::hasColumn('produk', 'stok_minimum')) {
                $table->unsignedInteger('stok_minimum')->default(5)->after('scheduled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $cols = ['spesifikasi','gender','tipe_olahraga','tags','status_publish','scheduled_at','stok_minimum'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('produk', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};

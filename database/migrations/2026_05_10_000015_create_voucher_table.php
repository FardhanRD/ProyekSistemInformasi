<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('voucher')) {
            return;
        }

        Schema::create('voucher', function (Blueprint $table) {
            $table->id('voucher_id');
            $table->string('kode_voucher', 50)->unique();
            $table->string('nama_voucher', 150);
            $table->text('deskripsi')->nullable();
            $table->enum('jenis_diskon', ['persen', 'nominal', 'ongkir'])->default('persen');
            $table->decimal('nilai_diskon', 15, 2)->default(0);
            $table->decimal('min_belanja', 15, 2)->default(0);
            $table->decimal('maks_diskon', 15, 2)->nullable();
            $table->unsignedInteger('kuota')->nullable();
            $table->unsignedInteger('kuota_terpakai')->default(0);
            $table->dateTime('berlaku_mulai');
            $table->dateTime('berlaku_sampai');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};

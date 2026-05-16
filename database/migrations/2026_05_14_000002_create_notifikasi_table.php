<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifikasi')) {
            return;
        }

        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('notifikasi_id');
            $table->unsignedBigInteger('pengguna_id');
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('jenis', 50);
            $table->string('url_redirect', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('pengguna_id')->references('pengguna_id')->on('pengguna')->cascadeOnDelete();
            $table->index(['pengguna_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tracking_log')) {
            return;
        }

        Schema::create('tracking_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('pesanan_id');
            $table->foreign('pesanan_id')->references('pesanan_id')->on('pesanan')->cascadeOnDelete();
            $table->string('status', 100);
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->dateTime('waktu_update')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_log');
    }
};

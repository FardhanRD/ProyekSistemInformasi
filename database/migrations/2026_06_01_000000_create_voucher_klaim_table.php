<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('voucher_klaim')) {
            return;
        }

        Schema::create('voucher_klaim', function (Blueprint $table) {
            $table->bigIncrements('klaim_id');
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('buyer_id');
            $table->string('status', 30)->default('aktif');
            $table->timestamp('diklaim_at')->nullable();
            $table->timestamp('digunakan_at')->nullable();
            $table->timestamps();

            $table->unique(['voucher_id', 'buyer_id'], 'voucher_klaim_unique');
            $table->index('voucher_id');
            $table->index('buyer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_klaim');
    }
};
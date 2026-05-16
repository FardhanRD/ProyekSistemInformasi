<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admin_log')) {
            return;
        }

        Schema::create('admin_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('aksi', 100);
            $table->string('tabel', 80)->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('cascade');
            $table->index('admin_id', 'idx_al_admin');
            $table->index('aksi', 'idx_al_aksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_log');
    }
};

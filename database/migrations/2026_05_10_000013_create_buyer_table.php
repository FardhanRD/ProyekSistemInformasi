<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('buyer')) {
            return;
        }

        Schema::create('buyer', function (Blueprint $table) {
            $table->id('buyer_id');
            $table->foreignId('pengguna_id')->unique()->constrained('pengguna', 'pengguna_id')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer');
    }
};

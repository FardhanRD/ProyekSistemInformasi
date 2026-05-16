<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ekspedisi')) {
            return;
        }

        Schema::create('ekspedisi', function (Blueprint $table) {
            $table->id('ekspedisi_id');
            $table->string('nama_ekspedisi', 100);
            $table->string('jenis_layanan', 80);
            $table->string('estimasi_hari', 30)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekspedisi');
    }
};

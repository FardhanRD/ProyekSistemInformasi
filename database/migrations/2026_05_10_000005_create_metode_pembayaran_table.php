<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('metode_pembayaran')) {
            return;
        }

        Schema::create('metode_pembayaran', function (Blueprint $table) {
            $table->id('metode_id');
            $table->string('metode', 100);
            $table->enum('jenis', ['transfer','ewallet','qris','cod','kartu_kredit']);
            $table->string('logo_url', 500)->nullable();
            $table->text('instruksi')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pembayaran');
    }
};

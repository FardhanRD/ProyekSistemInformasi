<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('banner')) return;

        Schema::create('banner', function (Blueprint $table) {
            $table->id('banner_id');
            $table->string('judul', 200)->nullable();
            $table->string('sub_judul', 300)->nullable();
            $table->string('url_gambar', 500);
            $table->string('url_link', 500)->nullable();
            $table->tinyInteger('urutan')->unsigned()->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner');
    }
};

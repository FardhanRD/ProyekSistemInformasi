<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kategori')) {
            return;
        }

        Schema::create('kategori', function (Blueprint $table) {
            $table->id('kategori_id');
            $table->string('nama_kategori', 100);
            $table->string('slug', 120)->unique();
            $table->string('ikon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('level')->unsigned()->default(1);
            $table->tinyInteger('urutan')->unsigned()->default(0);
            $table->string('banner_url', 500)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('parent_id')->references('kategori_id')->on('kategori')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};

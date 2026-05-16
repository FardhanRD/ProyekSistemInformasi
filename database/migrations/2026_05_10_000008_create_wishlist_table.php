<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wishlist')) {
            return;
        }

        Schema::create('wishlist', function (Blueprint $table) {
            $table->id('wishlist_id');
            $table->foreignId('pengguna_id')->constrained('pengguna', 'pengguna_id')->onDelete('cascade');
            $table->unsignedBigInteger('produk_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['pengguna_id', 'produk_id']);
            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};

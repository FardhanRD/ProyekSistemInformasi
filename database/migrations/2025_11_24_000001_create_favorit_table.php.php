<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('favorites')) {
            return;
        }

        // Kita buat tabel dengan nama 'favorites' (Inggris)
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            
            // Hubungkan ke tabel 'users' dan 'products'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->timestamps();

            // Mencegah user menyukai produk yang sama 2 kali
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
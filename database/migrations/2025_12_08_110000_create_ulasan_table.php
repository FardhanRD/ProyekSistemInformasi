<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            
            // Gunakan 'produk_id' agar konsisten dengan naming convention 
            // di project (pembeli_id, produk_id, etc) 
            $table->foreignId('produk_id')
                  ->constrained('products') 
                  ->onDelete('cascade');

            // Foreign key ke users table
            $table->foreignId('pembeli_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Rating dari 1-5
            $table->tinyInteger('rating')->unsigned()->default(5); 
            
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
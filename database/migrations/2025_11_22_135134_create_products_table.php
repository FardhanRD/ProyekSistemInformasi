<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Kolom Data Produk (Wajib Bahasa Inggris sesuai Controller)
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 15, 0); // Menggunakan decimal untuk harga
            $table->integer('stock');
            $table->string('image')->nullable();

            // Kolom Relasi Kategori (Langsung disini)
            // 'nullable' artinya produk boleh tidak punya kategori
            // 'constrained' artinya nyambung ke id di tabel categories
            // 'onDelete set null' artinya jika kategori dihapus, produk tidak hilang (hanya kategorinya jadi kosong)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

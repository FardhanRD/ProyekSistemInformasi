<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_supplier')) {
            return;
        }

        Schema::create('produk_supplier', function (Blueprint $table) {
            $table->id('produk_supplier_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('produk_id');
            $table->decimal('harga_modal', 12, 2);
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('supplier_id')->references('supplier_id')->on('supplier')->onDelete('cascade');
            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
            $table->unique(['supplier_id', 'produk_id']);
            $table->index('supplier_id');
            $table->index('produk_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_supplier');
    }
};

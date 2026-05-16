<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rating_toko')) {
            return;
        }

        Schema::create('rating_toko', function (Blueprint $table) {
            $table->id('rating_toko_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('buyer_id');
            $table->foreign('supplier_id')->references('supplier_id')->on('supplier')->onDelete('cascade');
            $table->foreign('buyer_id')->references('buyer_id')->on('buyer')->onDelete('cascade');
            $table->unsignedTinyInteger('bintang');
            $table->text('komentar')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['supplier_id', 'buyer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_toko');
    }
};

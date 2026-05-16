<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('promo')) return;

        Schema::create('promo', function (Blueprint $table) {
            $table->id('promo_id');
            $table->string('nama_promo', 150);
            $table->enum('jenis', ['flash_sale','diskon_produk','voucher']);
            $table->unsignedBigInteger('produk_id')->nullable()->comment('NULL = berlaku global');
            $table->decimal('persen_diskon', 5, 2)->nullable();
            $table->decimal('nominal_diskon', 15, 2)->nullable();
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};

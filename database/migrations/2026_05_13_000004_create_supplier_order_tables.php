<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_order')) {
            Schema::create('supplier_order', function (Blueprint $table) {
                $table->id('supplier_order_id');
                $table->unsignedBigInteger('supplier_id');
                $table->unsignedBigInteger('admin_id');
                $table->string('kode_order', 50)->unique()->comment('PO-20240101-001');
                $table->unsignedInteger('total_item')->default(0);
                $table->decimal('total_harga', 15, 2)->default(0);
                $table->enum('status', ['draft','dikirim','diterima','dibatalkan'])->default('draft');
                $table->text('catatan')->nullable();
                $table->timestamp('tanggal_order')->useCurrent();
                $table->dateTime('tanggal_diterima')->nullable();

                $table->foreign('supplier_id')->references('supplier_id')->on('supplier')->onDelete('restrict');
                $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('restrict');
                $table->index('supplier_id', 'idx_so_supplier');
                $table->index('status', 'idx_so_status');
            });
        }

        if (!Schema::hasTable('supplier_order_detail')) {
            Schema::create('supplier_order_detail', function (Blueprint $table) {
                $table->id('sod_id');
                $table->unsignedBigInteger('supplier_order_id');
                $table->unsignedBigInteger('detail_produk_id');
                $table->unsignedInteger('qty');
                $table->decimal('harga_beli', 15, 2);
                $table->decimal('subtotal', 15, 2);

                $table->foreign('supplier_order_id')->references('supplier_order_id')->on('supplier_order')->onDelete('cascade');
                $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_order_detail');
        Schema::dropIfExists('supplier_order');
    }
};

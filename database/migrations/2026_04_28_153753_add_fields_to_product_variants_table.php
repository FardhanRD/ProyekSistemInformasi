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
        Schema::table('product_variants', function (Blueprint $table) {
            // Add product_id foreign key if not exists
            if (!Schema::hasColumn('product_variants', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            }
            // Add variant name field
            if (!Schema::hasColumn('product_variants', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('color');
            }
            // Add initial_stock field
            if (!Schema::hasColumn('product_variants', 'initial_stock')) {
                $table->integer('initial_stock')->default(0)->after('variant_name');
            }
            // Add min_stock field
            if (!Schema::hasColumn('product_variants', 'min_stock')) {
                $table->integer('min_stock')->default(10)->after('initial_stock');
            }
            // Add price_adjustment field
            if (!Schema::hasColumn('product_variants', 'price_adjustment')) {
                $table->decimal('price_adjustment', 10, 2)->nullable()->after('min_stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['product_id']);
            $table->dropColumnIfExists(['product_id', 'variant_name', 'initial_stock', 'min_stock', 'price_adjustment']);
        });
    }
};

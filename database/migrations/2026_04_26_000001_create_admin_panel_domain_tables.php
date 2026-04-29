<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }

            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'blocked_at')) {
                $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            }

            if (!Schema::hasColumn('users', 'blocked_reason')) {
                $table->string('blocked_reason')->nullable()->after('blocked_at');
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable()->after('phone_number');
            }

            if (!Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('email');
            }
        });

        if (!Schema::hasTable('master_products')) {
            Schema::create('master_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('brand');
            $table->json('specifications')->nullable();
            $table->enum('gender', ['unisex', 'male', 'female', 'kids'])->default('unisex');
            $table->string('sport_type');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_product_id')->constrained('master_products')->cascadeOnDelete();
            $table->string('size', 20);
            $table->string('color', 50);
            $table->string('sku')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['master_product_id', 'size', 'color']);
            });
        }

        if (!Schema::hasTable('product_variant_prices')) {
            Schema::create('product_variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->decimal('base_price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('flash_sale_price', 15, 2)->nullable();
            $table->dateTime('flash_sale_start')->nullable();
            $table->dateTime('flash_sale_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_media')) {
            Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_product_id')->nullable()->constrained('master_products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('supplier_products')) {
            Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('master_product_id')->constrained('master_products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->decimal('purchase_price', 15, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['supplier_id', 'master_product_id', 'product_variant_id'], 'supplier_product_variant_unique');
            });
        }

        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->unique()->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamp('last_restock_at')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->enum('movement_type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('before_qty');
            $table->integer('after_qty');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            });
        }

        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('order_date');
            $table->enum('status', ['draft', 'ordered', 'received', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique()->after('payment_url');
            }

            if (!Schema::hasColumn('orders', 'verified_paid_at')) {
                $table->timestamp('verified_paid_at')->nullable()->after('invoice_number');
            }

            if (!Schema::hasColumn('orders', 'stock_reduced_at')) {
                $table->timestamp('stock_reduced_at')->nullable()->after('verified_paid_at');
            }

            if (!Schema::hasColumn('orders', 'courier_service')) {
                $table->string('courier_service')->nullable()->after('stock_reduced_at');
            }

            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('courier_service');
            }

            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('tracking_number');
            }

            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 15, 2)->default(0)->after('shipping_status');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->nullable()->after('price');
            }
        });

        Schema::table('ulasan', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan', 'moderation_status')) {
                $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('approved')->after('komentar');
            }

            if (!Schema::hasColumn('ulasan', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('moderation_status');
            }

            if (!Schema::hasColumn('ulasan', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('admin_reply');
            }

            if (!Schema::hasColumn('ulasan', 'moderated_by')) {
                $table->foreignId('moderated_by')->nullable()->after('moderated_at')->constrained('users')->nullOnDelete();
            }
        });

        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->decimal('discount_value', 15, 2);
            $table->decimal('min_order', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->integer('quota')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_discounts')) {
            Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_product_id')->nullable()->constrained('master_products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->decimal('discount_value', 15, 2);
            $table->boolean('is_flash_sale')->default(false);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('shipping_settings')) {
            Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->string('destination_zone');
            $table->string('courier_service');
            $table->decimal('cost', 15, 2);
            $table->integer('estimated_days')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('audit_trails')) {
            Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->unsignedBigInteger('row_id');
            $table->string('action');
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['table_name', 'row_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('shipping_settings');
        Schema::dropIfExists('product_discounts');
        Schema::dropIfExists('vouchers');

        Schema::table('ulasan', function (Blueprint $table) {
            if (Schema::hasColumn('ulasan', 'moderated_by')) {
                $table->dropConstrainedForeignId('moderated_by');
            }
            if (Schema::hasColumn('ulasan', 'moderated_at')) {
                $table->dropColumn('moderated_at');
            }
            if (Schema::hasColumn('ulasan', 'admin_reply')) {
                $table->dropColumn('admin_reply');
            }
            if (Schema::hasColumn('ulasan', 'moderation_status')) {
                $table->dropColumn('moderation_status');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->dropConstrainedForeignId('product_variant_id');
            }
            if (Schema::hasColumn('order_items', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'invoice_number',
                'verified_paid_at',
                'stock_reduced_at',
                'courier_service',
                'tracking_number',
                'shipping_status',
                'shipping_cost',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_variant_prices');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('master_products');

        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('suppliers', 'email')) {
                $table->dropColumn('email');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'blocked_reason')) {
                $table->dropColumn('blocked_reason');
            }
            if (Schema::hasColumn('users', 'blocked_at')) {
                $table->dropColumn('blocked_at');
            }
            if (Schema::hasColumn('users', 'is_blocked')) {
                $table->dropColumn('is_blocked');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
        });
    }
};

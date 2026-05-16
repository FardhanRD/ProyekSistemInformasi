<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk')) {
            Schema::table('produk', function (Blueprint $table) {
                if (! Schema::hasColumn('produk', 'spesifikasi')) {
                    $table->text('spesifikasi')->nullable()->after('deskripsi');
                }

                if (! Schema::hasColumn('produk', 'gender')) {
                    $table->enum('gender', ['men', 'women', 'unisex', 'kids'])->default('unisex')->after('spesifikasi');
                }

                if (! Schema::hasColumn('produk', 'tipe_olahraga')) {
                    $table->string('tipe_olahraga', 80)->nullable()->after('gender');
                }

                if (! Schema::hasColumn('produk', 'tags')) {
                    $table->json('tags')->nullable()->after('tipe_olahraga');
                }

                if (! Schema::hasColumn('produk', 'status_publish')) {
                    $table->enum('status_publish', ['publish', 'draft', 'scheduled'])->default('draft')->after('tags');
                }

                if (! Schema::hasColumn('produk', 'scheduled_at')) {
                    $table->dateTime('scheduled_at')->nullable()->after('status_publish');
                }

                if (! Schema::hasColumn('produk', 'stok_minimum')) {
                    $table->unsignedInteger('stok_minimum')->default(5)->after('scheduled_at');
                }
            });
        }

        if (Schema::hasTable('supplier')) {
            Schema::table('supplier', function (Blueprint $table) {
                if (! Schema::hasColumn('supplier', 'kategori_supplier')) {
                    $table->string('kategori_supplier', 100)->nullable()->after('nama_toko');
                }

                if (! Schema::hasColumn('supplier', 'no_telepon')) {
                    $table->string('no_telepon', 20)->nullable()->after('kategori_supplier');
                }

                if (! Schema::hasColumn('supplier', 'email')) {
                    $table->string('email', 150)->nullable()->after('no_telepon');
                }

                if (! Schema::hasColumn('supplier', 'latitude')) {
                    $table->decimal('latitude', 10, 8)->nullable()->after('alamat_toko');
                }

                if (! Schema::hasColumn('supplier', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                }

                if (! Schema::hasColumn('supplier', 'global_rank')) {
                    $table->unsignedInteger('global_rank')->nullable()->after('longitude');
                }

                if (! Schema::hasColumn('supplier', 'total_orders')) {
                    $table->unsignedInteger('total_orders')->default(0)->after('global_rank');
                }
            });
        }

        if (Schema::hasTable('ekspedisi')) {
            Schema::table('ekspedisi', function (Blueprint $table) {
                if (! Schema::hasColumn('ekspedisi', 'ongkir_per_km')) {
                    $table->decimal('ongkir_per_km', 10, 2)->nullable()->after('estimasi_hari');
                }

                if (! Schema::hasColumn('ekspedisi', 'ongkir_flat')) {
                    $table->decimal('ongkir_flat', 10, 2)->nullable()->after('ongkir_per_km');
                }
            });

            DB::table('ekspedisi')->where('nama_ekspedisi', 'JNE')->where('jenis_layanan', 'REG')->update(['ongkir_flat' => 15000]);
            DB::table('ekspedisi')->where('nama_ekspedisi', 'JNE')->where('jenis_layanan', 'YES')->update(['ongkir_flat' => 25000]);
            DB::table('ekspedisi')->where('nama_ekspedisi', 'J&T')->where('jenis_layanan', 'EZ')->update(['ongkir_flat' => 13000]);
            DB::table('ekspedisi')->where('nama_ekspedisi', 'SiCepat')->where('jenis_layanan', 'HALU')->update(['ongkir_flat' => 20000]);
            DB::table('ekspedisi')->where('nama_ekspedisi', 'Anteraja')->where('jenis_layanan', 'Reguler')->update(['ongkir_flat' => 12000]);
            DB::table('ekspedisi')->where('nama_ekspedisi', 'GoSend')->where('jenis_layanan', 'Sameday')->update(['ongkir_flat' => 30000]);
        }

        if (! Schema::hasTable('stock_movement')) {
            Schema::create('stock_movement', function (Blueprint $table) {
                $table->id('movement_id');
                $table->unsignedBigInteger('detail_produk_id');
                $table->enum('jenis', ['in', 'out', 'adjustment']);
                $table->integer('qty');
                $table->unsignedInteger('stok_sebelum');
                $table->unsignedInteger('stok_sesudah');
                $table->string('referensi', 100)->nullable();
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('dibuat_oleh')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('detail_produk_id')->references('detail_produk_id')->on('detail_produk')->onDelete('cascade');
                $table->index('detail_produk_id', 'idx_sm_detail');
                $table->index('jenis', 'idx_sm_jenis');
            });
        }

        if (! Schema::hasTable('supplier_order')) {
            Schema::create('supplier_order', function (Blueprint $table) {
                $table->id('supplier_order_id');
                $table->unsignedBigInteger('supplier_id');
                $table->unsignedBigInteger('admin_id');
                $table->string('kode_order', 50)->unique();
                $table->unsignedInteger('total_item')->default(0);
                $table->decimal('total_harga', 15, 2)->default(0);
                $table->enum('status', ['draft', 'dikirim', 'diterima', 'dibatalkan'])->default('draft');
                $table->text('catatan')->nullable();
                $table->timestamp('tanggal_order')->useCurrent();
                $table->dateTime('tanggal_diterima')->nullable();

                $table->foreign('supplier_id')->references('supplier_id')->on('supplier')->onDelete('restrict');
                $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('restrict');
                $table->index('supplier_id', 'idx_so_supplier');
                $table->index('status', 'idx_so_status');
            });
        }

        if (! Schema::hasTable('supplier_order_detail')) {
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

        if (! Schema::hasTable('admin_log')) {
            Schema::create('admin_log', function (Blueprint $table) {
                $table->id('log_id');
                $table->unsignedBigInteger('admin_id');
                $table->string('aksi', 100);
                $table->string('tabel', 80)->nullable();
                $table->unsignedBigInteger('record_id')->nullable();
                $table->json('data_lama')->nullable();
                $table->json('data_baru')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 300)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('cascade');
                $table->index('admin_id', 'idx_al_admin');
                $table->index('aksi', 'idx_al_aksi');
            });
        }

        if (! Schema::hasTable('promo')) {
            Schema::create('promo', function (Blueprint $table) {
                $table->id('promo_id');
                $table->string('nama_promo', 150);
                $table->enum('jenis', ['flash_sale', 'diskon_produk', 'voucher']);
                $table->unsignedBigInteger('produk_id')->nullable();
                $table->decimal('persen_diskon', 5, 2)->nullable();
                $table->decimal('nominal_diskon', 15, 2)->nullable();
                $table->dateTime('mulai');
                $table->dateTime('selesai');
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('banner')) {
            Schema::create('banner', function (Blueprint $table) {
                $table->id('banner_id');
                $table->string('judul', 200)->nullable();
                $table->string('sub_judul', 300)->nullable();
                $table->string('url_gambar', 500);
                $table->string('url_link', 500)->nullable();
                $table->unsignedTinyInteger('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banner');
        Schema::dropIfExists('promo');
        Schema::dropIfExists('admin_log');
        Schema::dropIfExists('supplier_order_detail');
        Schema::dropIfExists('supplier_order');
        Schema::dropIfExists('stock_movement');

        if (Schema::hasTable('ekspedisi')) {
            Schema::table('ekspedisi', function (Blueprint $table) {
                if (Schema::hasColumn('ekspedisi', 'ongkir_flat')) {
                    $table->dropColumn('ongkir_flat');
                }

                if (Schema::hasColumn('ekspedisi', 'ongkir_per_km')) {
                    $table->dropColumn('ongkir_per_km');
                }
            });
        }

        if (Schema::hasTable('supplier')) {
            Schema::table('supplier', function (Blueprint $table) {
                foreach (['total_orders', 'global_rank', 'longitude', 'latitude', 'email', 'no_telepon', 'kategori_supplier'] as $column) {
                    if (Schema::hasColumn('supplier', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('produk')) {
            Schema::table('produk', function (Blueprint $table) {
                foreach (['stok_minimum', 'scheduled_at', 'status_publish', 'tags', 'tipe_olahraga', 'gender', 'spesifikasi'] as $column) {
                    if (Schema::hasColumn('produk', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            if (!Schema::hasColumn('ekspedisi','ongkir_per_km')) {
                $table->decimal('ongkir_per_km', 10, 2)->nullable()->after('estimasi_hari');
            }
            if (!Schema::hasColumn('ekspedisi','ongkir_flat')) {
                $table->decimal('ongkir_flat', 10, 2)->nullable()->after('ongkir_per_km');
            }
        });

        // Optional seed updates (best-effort; will not fail migration if rows missing)
        try {
            DB::table('ekspedisi')->where('nama_ekspedisi','JNE')->where('jenis_layanan','REG')->update(['ongkir_flat' => 15000]);
            DB::table('ekspedisi')->where('nama_ekspedisi','JNE')->where('jenis_layanan','YES')->update(['ongkir_flat' => 25000]);
            DB::table('ekspedisi')->where('nama_ekspedisi','J&T')->where('jenis_layanan','EZ')->update(['ongkir_flat' => 13000]);
            DB::table('ekspedisi')->where('nama_ekspedisi','SiCepat')->where('jenis_layanan','HALU')->update(['ongkir_flat' => 20000]);
            DB::table('ekspedisi')->where('nama_ekspedisi','Anteraja')->where('jenis_layanan','Reguler')->update(['ongkir_flat' => 12000]);
            DB::table('ekspedisi')->where('nama_ekspedisi','GoSend')->where('jenis_layanan','Sameday')->update(['ongkir_flat' => 30000]);
        } catch (\Throwable $e) {
            // swallow — seed best-effort
        }
    }

    public function down(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $cols = ['ongkir_per_km','ongkir_flat'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('ekspedisi', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};

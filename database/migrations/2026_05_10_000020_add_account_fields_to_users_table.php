<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (! Schema::hasColumn('pengguna', 'username')) {
                $table->string('username', 100)->nullable()->unique()->after('nama_pengguna');
            }

            if (! Schema::hasColumn('pengguna', 'no_telepon')) {
                $table->string('no_telepon', 25)->nullable()->after('email');
            }

            if (! Schema::hasColumn('pengguna', 'foto_profil')) {
                $table->string('foto_profil', 255)->nullable()->after('no_telepon');
            }
        });

        DB::table('pengguna')->orderBy('pengguna_id')->get()->each(function ($user) {
            if (blank($user->username)) {
                DB::table('pengguna')->where('pengguna_id', $user->pengguna_id)->update([
                    'username' => Str::slug($user->nama_pengguna) . '-' . $user->pengguna_id,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'foto_profil')) {
                $table->dropColumn('foto_profil');
            }
            if (Schema::hasColumn('pengguna', 'no_telepon')) {
                $table->dropColumn('no_telepon');
            }
            if (Schema::hasColumn('pengguna', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
        });
    }
};

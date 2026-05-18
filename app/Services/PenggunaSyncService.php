<?php

namespace App\Services;

use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PenggunaSyncService
{
    public function ensureForAuthUser(Pengguna $user, string $role = 'buyer', array $attributes = []): ?int
    {
        if (! Schema::hasTable('pengguna')) {
            return $user->pengguna_id;
        }

        $existing = DB::table('pengguna')->where('email', $user->email)->first();
        if ($existing) {
            DB::table('pengguna')->where('pengguna_id', $existing->pengguna_id)->update([
                'nama_pengguna' => $attributes['nama_pengguna'] ?? $user->name,
                'username' => $attributes['username'] ?? $user->username ?? $existing->username,
                'no_telepon' => $attributes['no_telepon'] ?? $user->no_telepon ?? $existing->no_telepon,
                'foto_profil' => $attributes['foto_profil'] ?? $user->foto_profil ?? $existing->foto_profil,
                'foto_profil_position' => $attributes['foto_profil_position'] ?? $user->foto_profil_position ?? $existing->foto_profil_position ?? '50% 50%',
                'role' => $role,
                'is_active' => 1,
                'updated_at' => now(),
            ]);
            return (int) $existing->pengguna_id;
        }

        return DB::table('pengguna')->insertGetId([
            'nama_pengguna' => $attributes['nama_pengguna'] ?? $user->name,
            'username' => $attributes['username'] ?? $user->username ?? (Str::slug($user->name) . '-' . $user->pengguna_id),
            'email' => $user->email,
            'no_telepon' => $attributes['no_telepon'] ?? $user->no_telepon,
            'foto_profil' => $attributes['foto_profil'] ?? $user->foto_profil,
            'foto_profil_position' => $attributes['foto_profil_position'] ?? $user->foto_profil_position ?? '50% 50%',
            'sandi' => $user->password,
            'role' => $role,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

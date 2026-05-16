<?php

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Schema;

if (! function_exists('kirimNotifikasi')) {
    function kirimNotifikasi(int $pengguna_id, string $judul, string $pesan, string $jenis, ?string $url = null): ?Notifikasi
    {
        if (!Schema::hasTable('notifikasi')) {
            return null;
        }

        return Notifikasi::create([
            'pengguna_id' => $pengguna_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'jenis' => $jenis,
            'url_redirect' => $url,
        ]);
    }
}

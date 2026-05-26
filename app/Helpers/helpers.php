<?php

// Notifikasi dinonaktifkan — fungsi menjaga kompatibilitas pemanggilan.
if (! function_exists('kirimNotifikasi')) {
    function kirimNotifikasi(int $pengguna_id, string $judul, string $pesan, string $jenis, ?string $url = null)
    {
        // Fungsionalitas notifikasi telah dihapus; kembalikan null tanpa melakukan apa-apa.
        return null;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We add 'dikirim' and 'selesai' to the pesanan.status_pesanan enum.
     */
    public function up()
    {
        // Modify enum to include the new values. Adjust the list to match current schema plus new ones.
        DB::statement("ALTER TABLE `pesanan` MODIFY `status_pesanan` ENUM('menunggu_konfirmasi','dikonfirmasi','dikemas','siap_kirim','diserahkan_ke_kurir','dalam_pengiriman','tiba_di_tujuan','diterima','bermasalah','dikirim','selesai') NOT NULL DEFAULT 'menunggu_konfirmasi'");
    }

    /**
     * Reverse the migrations.
     *
     * This will remove 'dikirim' and 'selesai' from the enum. Use with caution.
     */
    public function down()
    {
        DB::statement("ALTER TABLE `pesanan` MODIFY `status_pesanan` ENUM('menunggu_konfirmasi','dikonfirmasi','dikemas','siap_kirim','diserahkan_ke_kurir','dalam_pengiriman','tiba_di_tujuan','diterima','bermasalah') NOT NULL DEFAULT 'menunggu_konfirmasi'");
    }
};

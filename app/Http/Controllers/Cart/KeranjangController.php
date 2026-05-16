<?php

// ── FILE: app/Http/Controllers/Cart/KeranjangController.php ──

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\CartController as LegacyCartController;

class KeranjangController extends LegacyCartController
{
    // Adapter untuk menyelaraskan path controller.
    // Mapping:
    // - index() sudah ada
    // - store/update/destroy akan dilayani oleh method legacy jika tersedia.
}


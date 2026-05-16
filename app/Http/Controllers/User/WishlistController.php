<?php

// ── FILE: app/Http/Controllers/User/WishlistController.php ──

namespace App\Http\Controllers\User;

use App\Http\Controllers\WishlistController as LegacyWishlistController;

class WishlistController extends LegacyWishlistController
{
    // Adapter untuk menyelaraskan path controller.
    // Mapping:
    // - index() sudah ada
    // - toggle() sudah ada
}


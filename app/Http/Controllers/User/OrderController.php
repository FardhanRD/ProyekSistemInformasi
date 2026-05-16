<?php

// ── FILE: app/Http/Controllers/User/OrderController.php ──

namespace App\Http\Controllers\User;

use App\Http\Controllers\OrderController as LegacyOrderController;

class OrderController extends LegacyOrderController
{
    // Adapter untuk menyelaraskan path controller.
    // Mapping:
    // - index()/show() ada di legacy
    // - tracking/rating* kemungkinan perlu penyesuaian saat implementasi penuh.
}


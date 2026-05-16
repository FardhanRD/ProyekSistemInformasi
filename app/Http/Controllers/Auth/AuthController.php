<?php

// ── FILE: app/Http/Controllers/Auth/AuthController.php ──

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\AuthController as LegacyAuthController;

class AuthController extends LegacyAuthController
{
    // Adapter agar class path sesuai spec routes/web.php.
    // Saat ini repo sudah punya implementasi register/login/logout di controller legacy.
}



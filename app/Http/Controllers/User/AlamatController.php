<?php

// ── FILE: app/Http/Controllers/User/AlamatController.php ──

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Placeholder controller (adapter) untuk memenuhi route spec.
// Saat ini project kamu menggunakan ProfileController untuk manajemen alamat.
// Nanti saat implementasi penuh, endpoint ini harus disambungkan ke logic alamat.
class AlamatController extends Controller
{
    // Pakai logic legacy yang sudah ada di ProfileController
    // tapi expose via endpoints spec agar tidak error.

    public function index()
    {
        return app('App\\Http\\Controllers\\ProfileController')->addresses();
    }

    public function store(Request $request)
    {
        return app('App\\Http\\Controllers\\ProfileController')->storeAddress($request);
    }

    public function update(Request $request, $id)
    {
        // legacy method: updateAddress(Request $request, $id)
        return app('App\\Http\\Controllers\\ProfileController')->updateAddress($request, $id);
    }

    public function destroy($id)
    {
        return app('App\\Http\\Controllers\\ProfileController')->deleteAddress($id);
    }

    public function jadikanUtama(Request $request, $id)
    {
        // legacy: jadikan utama memakai is_utama via updateAddress.
        $request->merge(['is_utama' => true]);
        return app('App\\Http\\Controllers\\ProfileController')->updateAddress($request, $id);
    }
}



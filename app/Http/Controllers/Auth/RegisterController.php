<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('pengguna', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')],
            'no_telepon' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        $pengguna = Pengguna::create([
            'nama_pengguna' => $data['nama_lengkap'],
            'username' => $data['username'],
            'email' => $data['email'],
            'no_telepon' => $data['no_telepon'],
            'sandi' => Hash::make($data['password']),
            'role' => 'buyer',
            'is_active' => true,
        ]);

        Buyer::updateOrCreate(
            ['pengguna_id' => $pengguna->pengguna_id],
            ['pengguna_id' => $pengguna->pengguna_id]
        );

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Selamat datang di MOVR! Akun Anda berhasil dibuat.');
    }
}

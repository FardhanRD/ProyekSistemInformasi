<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Buyer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authCredentials = [
            $field => $credentials['login'],
            // Laravel akan membaca kolom password via getAuthPasswordName() di model Pengguna
            'sandi' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Login berhasil.');
        }

        return back()->withErrors(['login' => 'Email / username atau password salah.'])->onlyInput('login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('pengguna', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')],
            'no_telepon' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $pengguna = Pengguna::create([
            'nama_pengguna' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'no_telepon' => $data['no_telepon'],
            'sandi' => Hash::make($data['password']),
            'role' => 'buyer',
            'is_active' => true,
        ]);

        if (Schema::hasTable('buyer')) {
            Buyer::firstOrCreate(
                ['pengguna_id' => $pengguna->pengguna_id],
                ['pengguna_id' => $pengguna->pengguna_id]
            );
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout berhasil.');
    }
}

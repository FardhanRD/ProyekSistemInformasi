<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Buyer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        // Supaya Laravel bisa membaca input JSON dari Flutter dengan aman
        $inputData = $request->isJson() ? $request->json()->all() : $request->all();

        // Validasi data berdasarkan input yang disatukan
        $validator = Validator::make($inputData, [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $validator->validated();
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // --- SOLUSI DEFINITIF: Cari user secara manual berdasarkan email/username ---
        $user = Pengguna::where($field, $credentials['login'])->first();

        // Lakukan pengecekan manual menggunakan Hash::check agar kebal dari bug kustom kolom 'sandi'
        if ($user && Hash::check($credentials['password'], $user->sandi)) {
            
            // Loginkan user ke sistem auth guard Laravel
            Auth::login($user, $request->boolean('remember'));

            // --- JALUR KHUSUS MOBILE FLUTTER (Mengembalikan JSON & Token) ---
            if ($request->wantsJson() || $request->isJson()) {
                // Pastikan model Pengguna sudah memakai trait HasApiTokens dari Sanctum
                $token = method_exists($user, 'createToken') 
                    ? $user->createToken('auth_token')->plainTextToken 
                    : 'dummy_token_sanctum_belum_aktif';

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login berhasil melalui Mobile API',
                    'access_token' => $token,
                    'user' => $user
                ], 200);
            }

            // --- JALUR KHUSUS WEBSITE ---
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Login berhasil.');
        }

        // Jika Gagal Login (User tidak ada atau password salah)
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json([
                'message' => 'Email / username atau password salah.'
            ], 401);
        }

        return back()->withErrors(['login' => 'Email / username atau password salah.'])->onlyInput('login');
    }

    public function register(Request $request)
    {
        // Penyelarasan input JSON untuk Flutter Mobile
        $inputData = $request->isJson() ? $request->json()->all() : $request->all();

        $validator = Validator::make($inputData, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('pengguna', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')],
            'no_telepon' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6'], 
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'message' => 'Validasi register gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

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

        // --- JALUR KHUSUS MOBILE FLUTTER SETELAH REGISTER (Auto Login & Token) ---
        if ($request->wantsJson() || $request->isJson()) {
            $token = method_exists($pengguna, 'createToken') 
                ? $pengguna->createToken('auth_token')->plainTextToken 
                : 'dummy_token_sanctum_belum_aktif';

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil melalui Mobile API',
                'access_token' => $token,
                'user' => $pengguna
            ], 201);
        }

        // --- JALUR KHUSUS WEBSITE ---
        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        // Jalur Logout untuk Mobile
        if ($request->wantsJson() || $request->isJson()) {
            $user = Auth::user();
            if ($user && method_exists($user, 'currentAccessToken')) {
                $user->currentAccessToken()->delete(); // Hapus Token Sanctum aktif
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil dari Mobile'
            ], 200);
        }

        // Jalur Logout untuk Website
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout berhasil.');
    }
}
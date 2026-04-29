<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('movr.auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Pengguna::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli', // Default role for registrants
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('status', 'Registrasi berhasil!');
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('movr.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect based on role
            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ]);
    }

    /**
     * Redirect user to Google authentication page
     */
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google Sign-In belum diatur.',
            ]);
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle Google callback and authenticate user
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $email = $googleUser->getEmail();
            if (!$email) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Google tidak memiliki email yang valid.',
                ]);
            }

            $user = Pengguna::where('email', $email)->first();

            if (!$user) {
                $fallbackName = 'User ' . Str::upper(Str::random(4));

                $user = Pengguna::create([
                    'name' => $googleUser->getName() ?: $fallbackName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'pembeli',
                ]);

                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/');
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')->withErrors([
                'email' => 'Login Google gagal. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
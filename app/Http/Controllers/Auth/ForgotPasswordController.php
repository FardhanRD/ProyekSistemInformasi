<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form for requesting a password reset link.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link / authorize redirect.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi.',
        ]);

        $loginInput = $request->input('login');

        // Look up the user by email or username
        $user = Pengguna::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['login' => 'Email atau username tidak ditemukan dalam sistem kami.'])
                ->onlyInput('login');
        }

        // Securely store the user ID allowed to reset password in session
        $request->session()->flash('reset_allowed_user_id', $user->pengguna_id);

        return redirect()->route('password.reset')
            ->with('success', 'Pengguna berhasil diverifikasi. Silakan masukkan password baru Anda.');
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('reset_allowed_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'Sesi reset password Anda tidak valid atau telah kedaluwarsa.']);
        }

        // Keep the session key flashed for the submit request
        $request->session()->keep(['reset_allowed_user_id']);

        $userId = $request->session()->get('reset_allowed_user_id');
        $user = Pengguna::find($userId);

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'Pengguna tidak ditemukan.']);
        }

        return view('auth.reset-password', compact('user'));
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        if (!$request->session()->has('reset_allowed_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'Sesi reset password Anda tidak valid atau telah kedaluwarsa.']);
        }

        $userId = $request->session()->get('reset_allowed_user_id');
        $user = Pengguna::find($userId);

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'Pengguna tidak ditemukan.']);
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Update the password directly on the sandi column and save
        $user->sandi = Hash::make($request->input('new_password'));
        $user->save();

        // Clear the session authorization
        $request->session()->forget('reset_allowed_user_id');

        return redirect()->route('login')
            ->with('success', 'Password Anda telah berhasil diubah. Silakan masuk kembali.');
    }
}

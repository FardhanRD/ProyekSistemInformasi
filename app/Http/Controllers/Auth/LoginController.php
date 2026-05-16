<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        if (str_contains($data['login'], '@')) {
            $request->validate([
                'login' => ['email'],
            ]);
        }

        $credentials = [
            filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $data['login'],
            'password' => $data['password'],
        ];

        // Avoid remember-token writes when legacy schema does not provide remember_token.
        $remember = $request->boolean('remember') && Schema::hasColumn('pengguna', 'remember_token');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['login' => 'Email / username atau password salah.'])
                ->onlyInput('login');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $role = strtolower(trim((string) $user->role));
            $isAdmin = $role === 'admin' || $user->admin()->exists();

            if ($isAdmin) {
                return redirect()->route('admin.dashboard');
            }
        }

        return redirect()->intended('/')->with('success', 'Login berhasil.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil.');
    }
}

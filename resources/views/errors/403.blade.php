<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <div class="mb-8">
                <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-red-500 mb-4">
                    403
                </div>
                <h1 class="text-4xl font-bold mb-2">Akses Ditolak</h1>
                <p class="text-slate-400 text-lg">Kamu tidak memiliki izin untuk mengakses halaman ini.</p>
            </div>

            <div class="space-y-3 mt-8">
                <a href="{{ route('home') }}" class="inline-block w-full rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-3 font-bold text-slate-900 hover:shadow-lg hover:shadow-cyan-500/50 transition">
                    ← Kembali ke Beranda
                </a>
                @auth
                    <a href="{{ route('profile.index') }}" class="inline-block w-full rounded-lg border border-slate-600 px-6 py-3 font-bold text-slate-300 hover:border-slate-400 hover:bg-slate-800/50 transition">
                        👤 Profil Saya
                    </a>
                @endauth
            </div>

            <div class="mt-12 pt-8 border-t border-slate-700">
                <p class="text-xs text-slate-500">Kode Error: 403 | <a href="/" class="text-cyan-400 hover:underline">Hubungi Dukungan</a></p>
            </div>
        </div>
    </div>
</body>
</html>

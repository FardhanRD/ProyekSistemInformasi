@extends('layouts.buyer')

@section('title', 'Lupa Password')

@section('content')
<div class="relative min-h-[calc(100vh-8rem)] overflow-hidden bg-white py-10 sm:py-14">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 -left-16 h-72 w-72 rounded-full bg-[#63a2bb]/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-16 h-72 w-72 rounded-full bg-[#63a2bb]/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto w-full max-w-xl px-4 sm:px-6">
        <div class="rounded-3xl border border-[#63a2bb]/25 bg-white p-8 shadow-[0_20px_60px_-24px_rgba(99,162,187,0.45)] sm:p-10">
            <div class="text-center">
                <div class="mx-auto mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#63a2bb] text-xl font-black text-white">M</div>
                <h1 class="text-2xl font-black text-slate-800">Lupa Password</h1>
                <p class="mt-2 text-sm text-slate-500">Fitur reset password belum diaktifkan. Silakan hubungi admin MOVR untuk bantuan akses akun.</p>
            </div>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('login') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-[#63a2bb] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#4f90aa]">Kembali ke Login</a>
                <a href="{{ route('register') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-[#63a2bb] hover:text-[#63a2bb]">Daftar Akun Baru</a>
            </div>
        </div>
    </div>
</div>
@endsection

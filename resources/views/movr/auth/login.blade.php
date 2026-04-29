@extends('movr.layouts.auth')

@section('content')
<div class="w-full max-w-6xl">
    <div class="mx-auto mb-6 hidden w-full max-w-5xl items-center justify-between rounded-full bg-black/35 px-3 py-2 text-white frost ring-1 ring-white/20 lg:flex">
        <div class="flex items-center gap-2">
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#252c2b]/90 text-white/85 transition hover:bg-[#1d2322]">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#252c2b]/90 text-white/85 transition hover:bg-[#1d2322]">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </div>

        <div class="mx-4 flex h-10 flex-1 items-center rounded-full bg-[#121716]/95 px-5">
            <span class="text-sm font-semibold text-white/80">AA</span>
            <span class="mx-auto text-[22px] font-semibold tracking-wide leading-none text-white">www.Movr.com</span>
            <i class="fas fa-redo-alt text-sm text-white/80"></i>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-white/10 text-white/90 transition hover:bg-white/20">
                <i class="fas fa-arrow-up-from-bracket text-sm"></i>
            </button>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-white/10 text-white/90 transition hover:bg-white/20">
                <i class="fas fa-plus text-sm"></i>
            </button>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-white/10 text-white/90 transition hover:bg-white/20">
                <i class="far fa-clone text-sm"></i>
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-[2.2rem] bg-white/95 shadow-2xl ring-1 ring-black/10 lg:grid lg:grid-cols-[1.02fr_1fr]">
        <div class="relative hidden min-h-[700px] overflow-hidden lg:block">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.28)), url('{{ asset('images/auth-side.jpg') }}');"></div>

            <div class="relative flex h-full flex-col justify-between p-8 text-accent-primary">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/75 px-4 py-2 text-sm font-semibold tracking-widest frost">
                    <span class="inline-flex -space-x-2">
                        <img class="h-9 w-9 rounded-full border-2 border-white object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="user">
                        <img class="h-9 w-9 rounded-full border-2 border-white object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="user">
                        <img class="h-9 w-9 rounded-full border-2 border-white object-cover" src="https://randomuser.me/api/portraits/women/17.jpg" alt="user">
                    </span>
                    <span>JOIN WITH 30K+ USERS!</span>
                    <span class="rounded-full bg-accent-primary px-3 py-1 text-xs font-bold text-[#f6e9e9]">12k</span>
                </div>

                <div>
                    <p class="font-heading text-6xl uppercase leading-[0.9] text-white">Back to<br>nature.</p>
                    <p class="mt-3 text-lg text-white/90">Let get started with your 30 days free trial</p>
                </div>
            </div>
        </div>

        <div class="relative bg-white px-7 py-10 sm:px-10 lg:px-11">
            <div class="mx-auto max-w-md">
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/movr-logo.png') }}" alt="MOVR" class="mx-auto h-14 w-auto">
                    <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900">Welcome back</h1>
                    <p class="mt-2 text-sm text-gray-500">Welcome back! Please enter your details</p>
                </div>

                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="text-sm font-semibold text-gray-600">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 pb-3 pt-1 text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-0" />
                    </div>

                    <div>
                        <label for="password" class="text-sm font-semibold text-gray-600">Password</label>
                        <input id="password" name="password" type="password" required class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 pb-3 pt-1 text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-0" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 text-gray-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            Remember for 30 days
                        </label>
                        <a href="#" class="font-semibold text-gray-700 underline hover:text-black">Forgot Password</a>
                    </div>

                    <button type="submit" class="group relative mt-1 w-full overflow-hidden rounded-2xl bg-accent-primary py-3 text-lg font-semibold text-[#f6e9e9] transition hover:bg-accent-dark">
                        <span class="relative z-10">Sign In</span>
                        <span class="absolute right-1 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-[#f2d7d8] text-xl font-bold text-accent-primary">↗</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-gray-900 hover:underline">Register</a>
                </p>

                <div class="my-5 flex items-center gap-3 text-sm text-gray-400">
                    <span class="h-px flex-1 bg-gray-200"></span>
                    <span>or</span>
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center gap-3 rounded-2xl border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-800 transition hover:border-gray-400 hover:bg-gray-50">
                    <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
                        <path fill="#FFC107" d="M43.611 20.083h-1.639V20H24v8h11.303C33.654 32.657 29.24 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.152 7.959 3.041l5.657-5.657C34.046 6.054 29.272 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                        <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 19.013 12 24 12c3.059 0 5.842 1.152 7.959 3.041l5.657-5.657C34.046 6.054 29.272 4 24 4c-7.681 0-14.347 4.337-17.694 10.691z"/>
                        <path fill="#4CAF50" d="M24 44c5.17 0 9.86-1.977 13.409-5.191l-6.19-5.238C29.147 35.091 26.668 36 24 36c-5.219 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.51 39.556 16.227 44 24 44z"/>
                        <path fill="#1976D2" d="M43.611 20.083H24v8h11.303c-.792 2.237-2.239 4.149-4.084 5.488l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
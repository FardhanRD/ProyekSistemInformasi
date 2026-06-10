<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('ui.forgot_password') }} — MOVR</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: system-ui, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-[#F8FAFB] flex">

  {{-- Kiri: Ilustrasi --}}
  <div class="hidden lg:flex lg:w-1/2 bg-[#63A2BB] 
              flex-col items-center justify-center p-12 
              relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
      {{-- Pattern background --}}
      @for($i = 0; $i < 20; $i++)
      <div class="absolute w-32 h-32 border-2 border-white 
                  rounded-full"
           style="top: {{ rand(0,100) }}%; 
                  left: {{ rand(0,100) }}%;
                  transform: translate(-50%,-50%)">
      </div>
      @endfor
    </div>
    <div class="relative text-center text-white">
      <h1 class="text-5xl font-black tracking-wider mb-4">
        MOVR
      </h1>
      <p class="text-xl text-white/80 font-light">
        Move With Style
      </p>
      <div class="mt-12 grid grid-cols-3 gap-4 
                  text-center">
        @foreach([
          ['num'=>'500+','label'=>'Produk'],
          ['num'=>'50K+','label'=>'Pelanggan'],
          ['num'=>'4.9','label'=>'Rating'],
        ] as $stat)
        <div class="bg-white/10 rounded-2xl p-4">
          <p class="text-2xl font-black">
            {{ $stat['num'] }}
          </p>
          <p class="text-xs text-white/70 mt-1">
            {{ $stat['label'] }}
          </p>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Kanan: Form Lupa Password --}}
  <div class="w-full lg:w-1/2 flex items-center 
              justify-center p-6 sm:p-12">
    <div class="w-full max-w-md">
      
      {{-- Logo Mobile --}}
      <div class="lg:hidden text-center mb-8">
        <span class="text-3xl font-black text-[#63A2BB]">
          MOVR
        </span>
      </div>

      <h2 class="text-2xl font-black text-gray-900 mb-2">
        {{ __('ui.forgot_password') }}
      </h2>
      <p class="text-gray-400 text-sm mb-8">
        Masukkan email atau username terdaftar Anda untuk memverifikasi akun.
      </p>

      @if($errors->any())
      <div class="bg-red-50 border border-red-200 
                  rounded-2xl p-4 mb-6">
        <p class="text-red-600 text-sm font-medium">
          {{ $errors->first() }}
        </p>
      </div>
      @endif

      @if(session('success'))
      <div class="bg-green-50 border border-green-200 
                  rounded-2xl p-4 mb-6">
        <p class="text-green-600 text-sm font-medium">
          {{ session('success') }}
        </p>
      </div>
      @endif

      <form action="{{ route('password.email') }}" method="POST">
        @csrf
        
        <div class="space-y-4">
          {{-- Email / Username --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Email atau Username
            </label>
            <input type="text" name="login"
                   value="{{ old('login') }}"
                   placeholder="nama@email.com atau username"
                   required
                   class="w-full px-4 py-3.5 rounded-2xl 
                          border-2 border-gray-200 
                          focus:border-[#63A2BB] 
                          focus:ring-2 
                          focus:ring-[#63A2BB]/20 
                          focus:outline-none text-sm 
                          transition placeholder-gray-400
                          @error('login') 
                            border-red-300 
                          @enderror">
            @error('login')
            <p class="text-red-500 text-xs mt-1.5 
                       flex items-center gap-1">
              {{ $message }}
            </p>
            @enderror
          </div>
        </div>

        <button type="submit"
                class="w-full mt-6 bg-[#63A2BB] text-white 
                       py-4 rounded-2xl font-bold text-sm 
                       hover:bg-[#4A8BA3] 
                       hover:-translate-y-0.5 
                       hover:shadow-lg 
                       hover:shadow-[#63A2BB]/30 
                       transition-all duration-200 
                       flex items-center justify-center gap-2">
          Verifikasi Akun
          <svg class="w-4 h-4" fill="none" 
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </button>
      </form>

      <div class="flex items-center justify-between mt-6">
        <a href="{{ route('login') }}"
           class="text-[#63A2BB] text-sm font-bold hover:underline">
          Kembali ke Login
        </a>
        <a href="{{ route('register') }}"
           class="text-gray-500 text-sm hover:underline">
          Buat Akun Baru
        </a>
      </div>
    </div>
  </div>
  
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

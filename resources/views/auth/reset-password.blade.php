<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — MOVR</title>
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

  {{-- Kanan: Form Reset Password --}}
  <div class="w-full lg:w-1/2 flex items-center 
              justify-center p-6 sm:p-12"
       x-data="{ showNewPass: false, showConfirmPass: false }">
    <div class="w-full max-w-md">
      
      {{-- Logo Mobile --}}
      <div class="lg:hidden text-center mb-8">
        <span class="text-3xl font-black text-[#63A2BB]">
          MOVR
        </span>
      </div>

      <h2 class="text-2xl font-black text-gray-900 mb-2">
        Buat Password Baru
      </h2>
      <p class="text-gray-400 text-sm mb-8">
        Akun diverifikasi untuk <strong>{{ $user->nama_pengguna }}</strong> ({{ $user->email ?? $user->username }}). Silakan buat password baru minimal 8 karakter.
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

      <form action="{{ route('password.update') }}" method="POST">
        @csrf
        
        <div class="space-y-4">
          
          {{-- Password Baru --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Password Baru
            </label>
            <div class="relative">
              <input :type="showNewPass ? 'text' : 'password'"
                     name="new_password"
                     placeholder="Minimal 8 karakter"
                     required
                     class="w-full px-4 py-3.5 pr-12 
                            rounded-2xl border-2 
                            border-gray-200 
                            focus:border-[#63A2BB] 
                            focus:ring-2 
                            focus:ring-[#63A2BB]/20 
                            focus:outline-none text-sm 
                            transition">
              <button type="button"
                      @click="showNewPass = !showNewPass"
                      class="absolute right-4 top-1/2 
                             -translate-y-1/2 text-gray-400 
                             hover:text-gray-600">
                <svg x-show="!showNewPass" class="w-5 h-5" 
                     fill="none" stroke="currentColor" 
                     viewBox="0 0 24 24">
                  <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 
                           0z M2.458 12C3.732 7.943 7.523 
                           5 12 5c4.478 0 8.268 2.943 
                           9.542 7-1.274 4.057-5.064 7
                           -9.542 7-4.477 0-8.268-2.943
                           -9.542-7z"/>
                </svg>
                <svg x-show="showNewPass" x-cloak 
                     class="w-5 h-5" fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                  <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 
                           0112 19c-4.478 0-8.268-2.943
                           -9.543-7a9.97 9.97 0 011.563
                           -3.029m5.858.908a3 3 0 114.243 
                           4.243M9.878 9.878l4.242 4.242
                           M9.88 9.88l-3.29-3.29m7.532 
                           7.532l3.29 3.29M3 3l3.59 3.59
                           m0 0A9.953 9.953 0 0112 5c4.478 
                           0 8.268 2.943 9.543 7a10.025 
                           10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>

          {{-- Konfirmasi Password Baru --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Konfirmasi Password Baru
            </label>
            <div class="relative">
              <input :type="showConfirmPass ? 'text' : 'password'"
                     name="new_password_confirmation"
                     placeholder="Ulangi password baru"
                     required
                     class="w-full px-4 py-3.5 pr-12 
                            rounded-2xl border-2 
                            border-gray-200 
                            focus:border-[#63A2BB] 
                            focus:ring-2 
                            focus:ring-[#63A2BB]/20 
                            focus:outline-none text-sm 
                            transition">
              <button type="button"
                      @click="showConfirmPass = !showConfirmPass"
                      class="absolute right-4 top-1/2 
                             -translate-y-1/2 text-gray-400 
                             hover:text-gray-600">
                <svg x-show="!showConfirmPass" class="w-5 h-5" 
                     fill="none" stroke="currentColor" 
                     viewBox="0 0 24 24">
                  <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 
                           0z M2.458 12C3.732 7.943 7.523 
                           5 12 5c4.478 0 8.268 2.943 
                           9.542 7-1.274 4.057-5.064 7
                           -9.542 7-4.477 0-8.268-2.943
                           -9.542-7z"/>
                </svg>
                <svg x-show="showConfirmPass" x-cloak 
                     class="w-5 h-5" fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                  <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 
                           0112 19c-4.478 0-8.268-2.943
                           -9.543-7a9.97 9.97 0 011.563
                           -3.029m5.858.908a3 3 0 114.243 
                           4.243M9.878 9.878l4.242 4.242
                           M9.88 9.88l-3.29-3.29m7.532 
                           7.532l3.29 3.29M3 3l3.59 3.59
                           m0 0A9.953 9.953 0 0112 5c4.478 
                           0 8.268 2.943 9.543 7a10.025 
                           10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
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
          Perbarui Password
          <svg class="w-4 h-4" fill="none" 
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </button>
      </form>

      <div class="flex items-center justify-center mt-6">
        <a href="{{ route('password.request') }}"
           class="text-[#63A2BB] text-sm font-bold hover:underline">
          Batal dan Kembali
        </a>
      </div>
    </div>
  </div>
  
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

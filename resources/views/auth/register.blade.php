<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, 
        initial-scale=1.0">
  <title>Daftar — MOVR</title>
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

  {{-- Kanan: Form Register --}}
  <div class="w-full lg:w-1/2 flex items-center 
              justify-center p-6 sm:p-12 overflow-y-auto">
    <div class="w-full max-w-md py-8">
      
      {{-- Logo Mobile --}}
      <div class="lg:hidden text-center mb-8">
        <span class="text-3xl font-black text-[#63A2BB]">
          MOVR
        </span>
      </div>

      <h2 class="text-2xl font-black text-gray-900 mb-2">
        Daftar Akun MOVR
      </h2>
      <p class="text-gray-400 text-sm mb-8">
        Gabung sekarang dan nikmati pengalaman belanja terbaik
      </p>

      @if($errors->any())
      <div class="bg-red-50 border border-red-200 
                  rounded-2xl p-4 mb-6">
        <ul class="text-red-600 text-sm space-y-1">
          @foreach($errors->all() as $error)
          <li>• {{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form action="{{ route('register') }}" method="POST"
            x-data="{ 
              showPass: false, 
              showConfirm: false,
              passwordMatch: true,
              termsChecked: false,
              validatePassword() {
                const pass = document.querySelector('[name=password]');
                const conf = document.querySelector('[name=password_confirmation]');
                this.passwordMatch = pass.value === conf.value && pass.value.length >= 8;
              }
            }">
        @csrf
        
        <div class="space-y-4">
          
          {{-- Nama Lengkap --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Nama Lengkap
            </label>
            <input type="text" name="nama_lengkap"
                   value="{{ old('nama_lengkap') }}"
                   placeholder="Nama Anda"
                   required
                   class="w-full px-4 py-3.5 rounded-2xl 
                          border-2 border-gray-200 
                          focus:border-[#63A2BB] 
                          focus:ring-2 
                          focus:ring-[#63A2BB]/20 
                          focus:outline-none text-sm 
                          transition placeholder-gray-400
                          @error('nama_lengkap') 
                            border-red-300 
                          @enderror">
            @error('nama_lengkap')
            <p class="text-red-500 text-xs mt-1.5">
              {{ $message }}
            </p>
            @enderror
          </div>

          {{-- Username --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Username
            </label>
            <input type="text" name="username"
                   value="{{ old('username') }}"
                   placeholder="Username unik"
                   required
                   class="w-full px-4 py-3.5 rounded-2xl 
                          border-2 border-gray-200 
                          focus:border-[#63A2BB] 
                          focus:ring-2 
                          focus:ring-[#63A2BB]/20 
                          focus:outline-none text-sm 
                          transition placeholder-gray-400
                          @error('username') 
                            border-red-300 
                          @enderror">
            @error('username')
            <p class="text-red-500 text-xs mt-1.5">
              {{ $message }}
            </p>
            @enderror
          </div>

          {{-- Email --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Email
            </label>
            <input type="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="nama@email.com"
                   required
                   class="w-full px-4 py-3.5 rounded-2xl 
                          border-2 border-gray-200 
                          focus:border-[#63A2BB] 
                          focus:ring-2 
                          focus:ring-[#63A2BB]/20 
                          focus:outline-none text-sm 
                          transition placeholder-gray-400
                          @error('email') 
                            border-red-300 
                          @enderror">
            @error('email')
            <p class="text-red-500 text-xs mt-1.5">
              {{ $message }}
            </p>
            @enderror
          </div>

          {{-- No. Telepon --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              No. Telepon
            </label>
            <input type="tel" name="no_telepon"
                   value="{{ old('no_telepon') }}"
                   placeholder="+62 8xx xxxx xxxx"
                   required
                   class="w-full px-4 py-3.5 rounded-2xl 
                          border-2 border-gray-200 
                          focus:border-[#63A2BB] 
                          focus:ring-2 
                          focus:ring-[#63A2BB]/20 
                          focus:outline-none text-sm 
                          transition placeholder-gray-400
                          @error('no_telepon') 
                            border-red-300 
                          @enderror">
            @error('no_telepon')
            <p class="text-red-500 text-xs mt-1.5">
              {{ $message }}
            </p>
            @enderror
          </div>

          {{-- Password --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Password
            </label>
            <div class="relative">
              <input :type="showPass ? 'text' : 'password'"
                     name="password"
                     placeholder="Minimal 8 karakter"
                     required
                     @input="validatePassword()"
                     class="w-full px-4 py-3.5 pr-12 
                            rounded-2xl border-2 
                            border-gray-200 
                            focus:border-[#63A2BB] 
                            focus:ring-2 
                            focus:ring-[#63A2BB]/20 
                            focus:outline-none text-sm 
                            transition placeholder-gray-400">
              <button type="button"
                      @click="showPass = !showPass"
                      class="absolute right-4 top-1/2 
                             -translate-y-1/2 text-gray-400 
                             hover:text-gray-600">
                <svg x-show="!showPass" class="w-5 h-5" 
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
                <svg x-show="!showPass" x-cloak 
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
            @error('password')
            <p class="text-red-500 text-xs mt-1.5">
              {{ $message }}
            </p>
            @enderror
          </div>

          {{-- Konfirmasi Password --}}
          <div>
            <label class="block text-sm font-semibold 
                           text-gray-700 mb-2">
              Konfirmasi Password
            </label>
            <div class="relative">
              <input :type="showConfirm ? 'text' : 'password'"
                     name="password_confirmation"
                     placeholder="Ulangi password"
                     required
                     @input="validatePassword()"
                     class="w-full px-4 py-3.5 pr-12 
                            rounded-2xl border-2 
                            border-gray-200 
                            focus:border-[#63A2BB] 
                            focus:ring-2 
                            focus:ring-[#63A2BB]/20 
                            focus:outline-none text-sm 
                            transition placeholder-gray-400"
                     :class="{ 'border-red-300': !passwordMatch }">
              <button type="button"
                      @click="showConfirm = !showConfirm"
                      class="absolute right-4 top-1/2 
                             -translate-y-1/2 text-gray-400 
                             hover:text-gray-600">
                <svg x-show="!showConfirm" class="w-5 h-5" 
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
                <svg x-show="!showConfirm" x-cloak 
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
            <p x-show="!passwordMatch" x-cloak 
               class="text-red-500 text-xs mt-1.5">
              Password tidak cocok
            </p>
          </div>

          {{-- Terms Checkbox --}}
          <div class="flex items-start gap-3 pt-2">
            <input type="checkbox" name="terms"
                   id="terms"
                   required
                   x-model="termsChecked"
                   class="w-4 h-4 mt-1 rounded 
                          accent-[#63A2BB] cursor-pointer">
            <label for="terms" 
                   class="text-sm text-gray-600 cursor-pointer">
              Saya setuju dengan 
              <a href="#" class="text-[#63A2BB] 
                         font-semibold hover:underline">
                Syarat & Ketentuan
              </a>
              dan 
              <a href="#" class="text-[#63A2BB] 
                         font-semibold hover:underline">
                Kebijakan Privasi
              </a>
              MOVR
            </label>
          </div>
          @error('terms')
          <p class="text-red-500 text-xs mt-1.5">
            {{ $message }}
          </p>
          @enderror
        </div>

        <button type="submit"
                :disabled="!passwordMatch || !termsChecked"
                class="w-full mt-6 bg-[#63A2BB] text-white 
                       py-4 rounded-2xl font-bold text-sm 
                       hover:bg-[#4A8BA3] 
                       hover:-translate-y-0.5 
                       hover:shadow-lg 
                       hover:shadow-[#63A2BB]/30 
                       transition-all duration-200 
                       flex items-center justify-center gap-2
                       disabled:opacity-50 disabled:cursor-not-allowed
                       disabled:hover:bg-[#63A2BB] disabled:hover:translate-y-0
                       disabled:hover:shadow-none">
          Daftar Sekarang
          <svg class="w-4 h-4" fill="none" 
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </button>
      </form>

      <p class="text-center text-sm text-gray-500 mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}"
           class="text-[#63A2BB] font-bold hover:underline">
          Masuk di sini
        </a>
      </p>
    </div>
  </div>
  
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
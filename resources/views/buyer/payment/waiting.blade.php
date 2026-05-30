@extends('layouts.buyer')
@section('title', 'Menunggu Verifikasi — MOVR')
@section('content')

<div class="min-h-[80vh] flex items-center 
            justify-center px-4 py-12"
     x-data="{
       progress: 0,
       dots: 1,
       init() {
         // Animasi progress bar
         setTimeout(() => { this.progress = 35 }, 300);
         setTimeout(() => { this.progress = 60 }, 1500);
         setTimeout(() => { this.progress = 80 }, 3000);
         
         // Animasi dots
         setInterval(() => {
           this.dots = this.dots >= 3 ? 1 : this.dots + 1;
         }, 600);
         
         // Auto check status setiap 30 detik
         setInterval(async () => {
           try {
             const res = await fetch(
               '/payment/{{ $transaksi->kode_transaksi }}/check-status',
               { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
             );
             const data = await res.json();
             if (data.verified) {
               window.location.href = '/orders?verified=1';
             }
           } catch(e) {}
         }, 30000);
       }
     }">

  <div class="w-full max-w-lg">
    
    {{-- Card Utama --}}
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
      
      {{-- Header Gradient --}}
      <div class="bg-gradient-to-br from-[#63A2BB] 
                  to-[#4A8BA3] px-8 py-10 text-center 
                  relative overflow-hidden">
        
        {{-- Background circles dekoratif --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 
                    bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 
                    bg-white/10 rounded-full"></div>
        <div class="absolute top-4 left-4 w-16 h-16 
                    bg-white/5 rounded-full"></div>
        
        {{-- Animasi ikon --}}
        <div class="relative z-10 mb-5">
          <div class="w-24 h-24 bg-white/20 rounded-full 
                      flex items-center justify-center 
                      mx-auto mb-1 relative">
            {{-- Ring animasi --}}
            <div class="absolute inset-0 rounded-full 
                        border-4 border-white/30 
                        animate-ping"></div>
            <div class="absolute inset-0 rounded-full 
                        border-4 border-white/20 
                        animate-pulse"
                 style="animation-delay: 0.5s"></div>
            {{-- Ikon --}}
            <svg class="w-12 h-12 text-white relative z-10" 
                 fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
        </div>
        
        <h1 class="text-2xl font-black text-white relative z-10">
          Pembayaran Diterima!
        </h1>
        <p class="text-white/80 text-sm mt-2 relative z-10">
          Tim kami sedang memverifikasi pembayaran kamu
        </p>
        
        {{-- Dots animasi --}}
        <div class="flex justify-center gap-2 mt-4 relative z-10">
          <div class="w-2 h-2 rounded-full bg-white/60"
               :class="dots >= 1 ? 'bg-white scale-125' : 'bg-white/40'"
               style="transition: all 0.3s"></div>
          <div class="w-2 h-2 rounded-full"
               :class="dots >= 2 ? 'bg-white scale-125' : 'bg-white/40'"
               style="transition: all 0.3s"></div>
          <div class="w-2 h-2 rounded-full"
               :class="dots >= 3 ? 'bg-white scale-125' : 'bg-white/40'"
               style="transition: all 0.3s"></div>
        </div>
      </div>

      {{-- Body --}}
      <div class="px-8 py-6">
        
        {{-- Info Pesanan --}}
        <div class="bg-[#63A2BB]/5 border border-[#63A2BB]/20 
                    rounded-2xl p-4 mb-5">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-gray-400 mb-1">
                Kode Transaksi
              </p>
              <p class="font-bold text-gray-800 text-sm font-mono">
                {{ $transaksi->kode_transaksi }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-400 mb-1">
                Total Pembayaran
              </p>
              <p class="font-black text-[#63A2BB] text-sm">
                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-400 mb-1">
                Metode Bayar
              </p>
              <p class="font-semibold text-gray-700 text-sm">
                {{ $transaksi->pembayaran?->metodePembayaran?->metode ?? '-' }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-400 mb-1">
                Status
              </p>
              <span class="inline-flex items-center gap-1.5 
                           text-xs font-bold px-2.5 py-1 
                           rounded-full bg-amber-50 text-amber-600">
                <span class="w-1.5 h-1.5 bg-amber-500 
                             rounded-full animate-pulse">
                </span>
                Menunggu Verifikasi
              </span>
            </div>
          </div>
        </div>

        {{-- Progress Steps --}}
        <div class="mb-6">
          <p class="text-xs font-bold text-gray-400 
                     uppercase tracking-wider mb-3">
            Proses Verifikasi
          </p>
          
          <div class="space-y-3">
            @foreach([
              ['icon'=>'upload','label'=>'Bukti Pembayaran Diterima',
               'sub'=>'File berhasil diupload','done'=>true],
              ['icon'=>'search','label'=>'Sedang Diverifikasi Admin',
               'sub'=>'Estimasi 1x24 jam kerja','active'=>true],
              ['icon'=>'check','label'=>'Pembayaran Dikonfirmasi',
               'sub'=>'Pesanan akan diproses','done'=>false],
              ['icon'=>'box','label'=>'Pesanan Diproses',
               'sub'=>'Produk sedang dikemas','done'=>false],
            ] as $step)
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full flex-shrink-0 
                          flex items-center justify-center
                          {{ isset($step['done']) && $step['done'] 
                             ? 'bg-green-500' 
                             : (isset($step['active']) 
                                ? 'bg-[#63A2BB]' 
                                : 'bg-gray-100') }}">
                @if(isset($step['done']) && $step['done'])
                <svg class="w-4 h-4 text-white" fill="none" 
                     stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" 
                        stroke-linejoin="round" stroke-width="2.5" 
                        d="M5 13l4 4L19 7"/>
                </svg>
                @elseif(isset($step['active']))
                <div class="w-2.5 h-2.5 bg-white rounded-full 
                            animate-pulse"></div>
                @else
                <div class="w-2.5 h-2.5 bg-gray-300 rounded-full">
                </div>
                @endif
              </div>
              <div class="flex-1 pb-1">
                <p class="text-sm font-semibold 
                           {{ isset($step['done']) && $step['done'] 
                              ? 'text-green-600' 
                              : (isset($step['active']) 
                                 ? 'text-[#63A2BB]' 
                                 : 'text-gray-400') }}">
                  {{ $step['label'] }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ $step['sub'] }}
                </p>
              </div>
              @if(isset($step['active']))
              <div class="flex-shrink-0">
                <svg class="animate-spin w-4 h-4 text-[#63A2BB]" 
                     fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" 
                          r="10" stroke="currentColor" 
                          stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
              </div>
              @endif
            </div>
            @endforeach
          </div>
        </div>

        {{-- Info Box --}}
        <div class="bg-amber-50 border border-amber-200 
                    rounded-2xl p-4 mb-5 flex gap-3">
          <div class="w-8 h-8 bg-amber-100 rounded-full 
                      flex items-center justify-center 
                      flex-shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" 
                 stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-bold text-amber-700">
              Informasi Penting
            </p>
            <p class="text-xs text-amber-600 mt-1 leading-relaxed">
              Verifikasi dilakukan dalam <strong>1×24 jam kerja</strong>.
              Kamu akan mendapat notifikasi setelah pembayaran 
              dikonfirmasi. Halaman ini otomatis refresh setiap 
              30 detik.
            </p>
          </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="space-y-2">
          <a href="{{ route('orders.index') }}"
             class="w-full flex items-center justify-center 
                    gap-2 bg-[#63A2BB] text-white py-4 
                    rounded-2xl font-bold text-sm 
                    hover:bg-[#4A8BA3] hover:-translate-y-0.5 
                    hover:shadow-lg hover:shadow-[#63A2BB]/30 
                    transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Lihat Pesanan Saya
          </a>
          
          <a href="{{ route('home') }}"
             class="w-full flex items-center justify-center 
                    gap-2 border-2 border-gray-200 text-gray-500 
                    py-3.5 rounded-2xl font-semibold text-sm 
                    hover:border-[#63A2BB] hover:text-[#63A2BB] 
                    transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Kembali ke Beranda
          </a>
        </div>
      </div>
    </div>

    {{-- Note bawah --}}
    <p class="text-center text-xs text-gray-400 mt-4">
      Ada pertanyaan? 
      <a href="#" class="text-[#63A2BB] hover:underline font-medium">
        Hubungi Customer Service
      </a>
    </p>
  </div>
</div>

@endsection

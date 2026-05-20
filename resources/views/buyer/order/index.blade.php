@extends('layouts.buyer')
@section('title', 'Pesanan Saya — MOVR')
@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-black text-gray-900 mb-6">
    Pesanan Saya
  </h1>

  {{-- Tab Status --}}
  <div class="flex gap-2 overflow-x-auto pb-3 mb-6 
              scrollbar-hide -mx-4 px-4">
    @php
      $tabs = [
        ''                      => 'Semua',
        'menunggu_pembayaran'   => 'Belum Bayar',
        'pembayaran_dikonfirmasi'=> 'Dikonfirmasi',
        'diproses'              => 'Dikemas',
        'dikirim'               => 'Dikirim',
        'selesai'               => 'Selesai',
        'dibatalkan'            => 'Dibatalkan',
      ];
      $activeTab = request('status', '');
    @endphp
    @foreach($tabs as $val => $label)
    @php
      $count = $orderCounts[$val] ?? 0;
    @endphp
    <a href="?status={{ $val }}"
       class="flex-shrink-0 flex items-center gap-1.5 
              px-4 py-2 rounded-full text-sm font-semibold 
              transition-all whitespace-nowrap
              {{ $activeTab === $val 
                 ? 'bg-[#63A2BB] text-white shadow-sm' 
                 : 'bg-white text-gray-500 border border-gray-200 hover:border-[#63A2BB] hover:text-[#63A2BB]' }}">
      {{ $label }}
      @if($val === 'menunggu_pembayaran' && ($orderCounts[$val] ?? 0) > 0)
      <span class="bg-red-500 text-white text-[10px] 
                   font-bold w-4 h-4 rounded-full 
                   flex items-center justify-center">
        {{ $orderCounts[$val] }}
      </span>
      @endif
    </a>
    @endforeach
  </div>

  {{-- List Orders --}}
  <div class="space-y-4">
    @forelse($transaksis as $t)
    @php
      $statusConfig = [
        'menunggu_pembayaran'     => ['color'=>'text-amber-600','bg'=>'bg-amber-50','border'=>'border-amber-200','label'=>'Menunggu Pembayaran','icon'=>'clock'],
        'pembayaran_dikonfirmasi' => ['color'=>'text-blue-600','bg'=>'bg-blue-50','border'=>'border-blue-200','label'=>'Pembayaran Dikonfirmasi','icon'=>'check'],
        'diproses'                => ['color'=>'text-purple-600','bg'=>'bg-purple-50','border'=>'border-purple-200','label'=>'Sedang Dikemas','icon'=>'box'],
        'dikirim'                 => ['color'=>'text-[#63A2BB]','bg'=>'bg-[#63A2BB]/5','border'=>'border-[#63A2BB]/30','label'=>'Dalam Pengiriman','icon'=>'truck'],
        'selesai'                 => ['color'=>'text-green-600','bg'=>'bg-green-50','border'=>'border-green-200','label'=>'Selesai','icon'=>'check-circle'],
        'dibatalkan'              => ['color'=>'text-red-500','bg'=>'bg-red-50','border'=>'border-red-200','label'=>'Dibatalkan','icon'=>'x'],
      ];
      $sc = $statusConfig[$t->status] ?? 
        ['color'=>'text-gray-500','bg'=>'bg-gray-50',
         'border'=>'border-gray-200','label'=>ucfirst($t->status),'icon'=>''];
      
      // Cek sudah dirating atau belum
      $sudahRating = false;
      if ($t->status === 'selesai') {
        $sudahRating = \App\Models\RatingProduk::where(
          'transaksi_id', $t->transaksi_id)
          ->where('buyer_id', auth()->user()->buyer->buyer_id)
          ->exists();
      }
    @endphp

    <div class="bg-white rounded-3xl shadow-sm 
                overflow-hidden border border-gray-100">
      
      {{-- Header --}}
      <div class="px-5 py-4 border-b border-gray-100 
                  flex items-center justify-between">
        <div class="flex items-center gap-3">
          {{-- Status Icon --}}
          <div class="w-9 h-9 rounded-full 
                      {{ $sc['bg'] }} 
                      flex items-center justify-center">
            @if($sc['icon'] === 'clock')
            <svg class="w-4 h-4 {{ $sc['color'] }}" 
                 fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @elseif($sc['icon'] === 'truck')
            <svg class="w-4 h-4 {{ $sc['color'] }}" 
                 fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4h4"/>
            </svg>
            @elseif($sc['icon'] === 'check-circle')
            <svg class="w-4 h-4 {{ $sc['color'] }}" 
                 fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @elseif($sc['icon'] === 'check')
            <svg class="w-4 h-4 {{ $sc['color'] }}" 
                 fill="none" stroke="currentColor" 
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7"/>
            </svg>
            @else
            <div class="w-2 h-2 rounded-full 
                        bg-current {{ $sc['color'] }}">
            </div>
            @endif
          </div>
          <div>
            <p class="text-xs text-gray-400">
              {{ is_string($t->tanggal) ? \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') : $t->tanggal->format('d M Y, H:i') }}
            </p>
            <p class="text-sm font-bold text-gray-700">
              {{ $t->kode_transaksi }}
            </p>
          </div>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 
                     rounded-full {{ $sc['bg'] }} 
                     {{ $sc['color'] }}">
          {{ $sc['label'] }}
        </span>
      </div>

      {{-- Item Preview --}}
      <div class="px-5 py-4">
        @foreach($t->details->take(2) as $d)
        <div class="flex items-center gap-3 
                    {{ !$loop->last ? 'mb-3' : '' }}">
          <img src="{{ $d->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png') }}"
               class="w-14 h-14 rounded-2xl object-cover 
                      flex-shrink-0 bg-gray-50">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-700 
                       line-clamp-1">
              {{ $d->nama_produk_snap }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
              {{ $d->ukuran_snap ?? 'No Size' }} · 
              {{ $d->warna_snap ?? 'No Color' }} · 
              x{{ $d->quantity }}
            </p>
          </div>
          <p class="text-sm font-bold text-gray-700 
                     flex-shrink-0">
            Rp {{ number_format($d->subtotal,0,',','.') }}
          </p>
        </div>
        @endforeach
        @if($t->details->count() > 2)
        <p class="text-xs text-gray-400 mt-2">
          +{{ $t->details->count()-2 }} produk lagi
        </p>
        @endif
      </div>

      {{-- Footer --}}
      <div class="px-5 py-4 bg-gray-50/50 
                  border-t border-gray-100
                  flex items-center justify-between gap-3">
        <div>
          <p class="text-xs text-gray-400">Total</p>
          <p class="font-black text-[#63A2BB] text-base">
            Rp {{ number_format($t->total_harga,0,',','.') }}
          </p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
          
          {{-- Tombol sesuai status --}}
          @if($t->status === 'menunggu_pembayaran')
          <a href="{{ route('payment.show', $t->kode_transaksi) }}"
             class="px-4 py-2 bg-[#63A2BB] text-white 
                    text-xs font-bold rounded-full 
                    hover:bg-[#4A8BA3] transition 
                    flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" 
                 stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Bayar Sekarang
          </a>
          
          @elseif(in_array($t->status, ['diproses','dikirim']))
          <a href="{{ route('tracking.show', $t->kode_transaksi) }}"
             class="px-4 py-2 border-2 border-[#63A2BB] 
                    text-[#63A2BB] text-xs font-bold 
                    rounded-full hover:bg-[#63A2BB] 
                    hover:text-white transition 
                    flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" 
                 stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" 
                    stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            Lacak Paket
          </a>
          
          @elseif($t->status === 'selesai' && !$sudahRating)
          <a href="{{ route('orders.rating', $t->kode_transaksi) }}"
             class="px-4 py-2 bg-amber-500 text-white 
                    text-xs font-bold rounded-full 
                    hover:bg-amber-600 transition 
                    flex items-center gap-1.5">
            ⭐ Beri Rating
          </a>
          
          @elseif($t->status === 'selesai' && $sudahRating)
          <span class="px-4 py-2 bg-green-50 
                       text-green-600 text-xs font-bold 
                       rounded-full flex items-center gap-1.5">
            ✓ Sudah Dirating
          </span>
          @endif
          
          <a href="{{ route('orders.show', $t->kode_transaksi) }}"
             class="px-4 py-2 bg-white border-2 
                    border-gray-200 text-gray-500 
                    text-xs font-semibold rounded-full 
                    hover:border-[#63A2BB] 
                    hover:text-[#63A2BB] transition">
            Detail
          </a>
        </div>
      </div>
    </div>
    @empty
    <div class="bg-white rounded-3xl p-16 shadow-sm 
                text-center">
      <div class="w-16 h-16 bg-[#63A2BB]/10 rounded-full 
                  flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-[#63A2BB]" fill="none" 
             stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" 
                stroke-linejoin="round" stroke-width="1.5"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
      </div>
      <h3 class="font-bold text-gray-600 mb-2">
        Belum ada pesanan
      </h3>
      <a href="/" class="mt-4 inline-flex px-6 py-2.5 
                          bg-[#63A2BB] text-white 
                          rounded-full font-semibold text-sm">
        Mulai Belanja
      </a>
    </div>
    @endforelse
  </div>
</div>
@endsection
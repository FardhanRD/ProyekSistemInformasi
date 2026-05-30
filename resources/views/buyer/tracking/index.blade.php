@extends('layouts.buyer')
@section('title', __('ui.tracking_title') . ' — MOVR')
@section('content')

@php
  $trackingStatus = (string) ($pesanan?->status_pesanan ?? '');
  $trackingStatusMap = [
    'menunggu_konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-amber-50 text-amber-600 border-amber-100', 'icon' => 'clock'],
    'dikemas' => ['label' => 'Dikemas', 'class' => 'bg-purple-50 text-purple-600 border-purple-100', 'icon' => 'package'],
    'siap_kirim' => ['label' => 'Siap Kirim', 'class' => 'bg-sky-50 text-sky-600 border-sky-100', 'icon' => 'truck'],
    'diserahkan_ke_kurir' => ['label' => 'Diserahkan ke Kurir', 'class' => 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20', 'icon' => 'truck'],
    'dalam_pengiriman' => ['label' => 'Dalam Pengiriman', 'class' => 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20', 'icon' => 'truck'],
    'tiba_di_tujuan' => ['label' => 'Tiba di Tujuan', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => 'check'],
    'diterima' => ['label' => 'Diterima', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => 'check'],
    'bermasalah' => ['label' => 'Bermasalah', 'class' => 'bg-red-50 text-red-600 border-red-100', 'icon' => 'alert'],
  ];
  $trackingStatusInfo = $trackingStatusMap[$trackingStatus] ?? [
    'label' => $trackingStatus !== '' ? ucfirst(str_replace('_', ' ', $trackingStatus)) : 'Belum Diproses',
    'class' => 'bg-gray-50 text-gray-500 border-gray-200',
    'icon' => 'clock',
  ];
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-400 mb-8">
    <a href="{{ route('orders.index') }}" 
       class="flex items-center gap-1 hover:text-[#63A2BB] transition">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.707 7.293a1 1 0 010 1.414L5.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z"/>
      </svg>
      {{ __('ui.tracking_back') }}
    </a>
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
    </svg>
    <span class="text-gray-700 font-medium">
      {{ __('ui.tracking_title') }}
    </span>
  </div>

  <h1 class="text-3xl font-black text-gray-900 mb-8">
    {{ __('ui.tracking_shipping') }}
  </h1>

  {{-- Header Card --}}
  <div class="bg-gradient-to-r from-[#63A2BB] to-[#5a93a8] 
              text-white rounded-3xl p-8 mb-8 shadow-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div>
        <p class="text-sm opacity-90 mb-1">{{ __('ui.tracking_courier') }}</p>
        <h2 class="text-2xl font-black mb-3">
          {{ $ekspedisi->nama_ekspedisi ?? __('ui.tracking_courier') }}
        </h2>
        <div class="space-y-2">
          @if($pesanan?->no_resi)
          <div class="flex items-center gap-2">
            <code class="bg-white/20 px-3 py-1.5 rounded-lg 
                        font-mono text-sm font-bold">
              {{ $pesanan->no_resi }}
            </code>
                <button onclick="navigator.clipboard.writeText('{{ $pesanan->no_resi }}').then(() => showToast(@json(__('ui.tracking_resi_copied'))))"
                    class="p-1 hover:bg-white/20 rounded transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
            </button>
          </div>
          @endif
          <p class="text-sm opacity-90">
            {{ $ekspedisi->jenis_layanan ?? __('ui.standard_service') }}
          </p>
        </div>
      </div>

      <div class="md:text-right">
        <p class="text-sm opacity-90 mb-1">{{ __('ui.tracking_eta') }}</p>
        <p class="text-2xl font-black mb-3">
          {{ $pesanan?->estimasi_tiba ? \Carbon\Carbon::parse($pesanan->estimasi_tiba)->locale('id')->format('d M Y') : '-' }}
        </p>
        <div class="mt-4 flex flex-wrap justify-end gap-3">
          <div class="inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur-sm">
            <span class="rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/90">Resi</span>
            <span class="font-mono text-sm">{{ $pesanan?->no_resi ?? '-' }}</span>
          </div>

          <div class="inline-flex items-center gap-2 rounded-2xl border bg-white px-4 py-2 text-sm font-semibold shadow-sm {{ $trackingStatusInfo['class'] }}">
            @if($trackingStatusInfo['icon'] === 'clock')
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            @elseif($trackingStatusInfo['icon'] === 'package')
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m-8-14l8 4m0 0v10m0-10L4 7m8 4l8-4"/>
              </svg>
            @elseif($trackingStatusInfo['icon'] === 'truck')
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6M3 17h1m16 0h1m-1-6h-5m0 0V6a1 1 0 00-1-1H6a2 2 0 00-2 2v10h2m12 0h2v-5a2 2 0 00-2-2h-5"/>
              </svg>
            @else
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            @endif
            <span>{{ $trackingStatusInfo['label'] }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Destination Address --}}
  <div class="bg-white rounded-2xl border-2 border-gray-100 p-6 mb-8">
    <div class="flex gap-4">
      <div class="flex-shrink-0">
        <div class="flex items-center justify-center w-12 h-12 rounded-full 
                    bg-[#63A2BB]/10">
          <svg class="w-6 h-6 text-[#63A2BB]" fill="none" 
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" 
                  stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
      </div>
      <div>
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">
          {{ __('ui.tracking_destination') }}
        </h3>
        <p class="font-bold text-gray-900 mb-1">
          {{ $alamatTujuan?->nama_penerima ?? __('ui.recipient') }} 
          ({{ $alamatTujuan?->no_telepon ?? '-' }})
        </p>
        <p class="text-gray-600 text-sm">
          {{ $alamatTujuan?->alamat_lengkap ?? __('ui.address_not_available') }}
        </p>
      </div>
    </div>
  </div>

  {{-- Timeline --}}
  <div class="bg-white rounded-2xl border-2 border-gray-100 p-8 mb-8">
    <h3 class="text-lg font-black text-gray-900 mb-8">
      {{ __('ui.tracking_status') }}
    </h3>

    @if($trackingLogs->count() > 0)
      <div class="relative space-y-6">
        {{-- Vertical Line --}}
        <div class="absolute left-6 top-0 bottom-0 w-1 bg-gradient-to-b 
                    from-[#63A2BB] to-gray-200"></div>

        @foreach($trackingLogs as $index => $log)
          <div class="relative pl-20">
            {{-- Dot --}}
            <div class="absolute left-0 top-1 w-12 h-12 rounded-full 
                        flex items-center justify-center 
                        {{ $index === 0 
                          ? 'bg-[#63A2BB] text-white' 
                          : 'bg-gray-200 text-gray-600' }} 
                        ring-8 ring-white font-bold">
              @if($index === 0)
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" 
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                </svg>
              @else
                {{ $index }}
              @endif
            </div>

            {{-- Content --}}
            <div>
              <h4 class="font-black text-gray-900 text-lg mb-1">
                {{ $log->status }}
              </h4>
              <p class="text-gray-600 text-sm mb-2">
                {{ $log->deskripsi }}
              </p>
              @if($log->lokasi)
                <p class="text-gray-500 text-xs flex items-center gap-1 mb-2">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" 
                          d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                  </svg>
                  {{ $log->lokasi }}
                </p>
              @endif
              <time class="text-xs font-bold text-gray-400">
                {{ \Carbon\Carbon::parse($log->waktu_update)->locale('id')->format('d M Y — H:i') }}
              </time>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" 
             stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">
          {{ __('ui.tracking_no_updates') }}
        </p>
      </div>
    @endif
  </div>

  {{-- Product Summary --}}
  <div class="bg-white rounded-2xl border-2 border-gray-100 overflow-hidden">
    <div class="px-8 py-5 border-b-2 border-gray-100 bg-gray-50">
      <h3 class="font-black text-gray-900">
        {{ __('ui.tracking_product_summary') }}
      </h3>
    </div>
    <div class="p-8 space-y-4">
@forelse($pesanan->details ?? [] as $detail)
        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
          @php
            $img = $detail->detailProduk->produk->gambarUtama;
          @endphp
          <img src="{{ $img?->url_safe ?? 'https://via.placeholder.com/80' }}"
               alt="{{ $detail->detailProduk->produk->nama_produk }}"
               class="w-16 h-16 rounded-lg object-cover bg-gray-100">
          
          <div class="flex-1">
            <h4 class="font-bold text-gray-900 mb-1">
              {{ $detail->detailProduk->produk->nama_produk }}
            </h4>
            <p class="text-sm text-gray-600">
              {{ $detail->jumlah }}x @ 
              Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
            </p>
            @if($detail->detailProduk->ukuran)
            <p class="text-xs text-gray-500 mt-1">
              {{ $detail->detailProduk->ukuran }}
              @if($detail->detailProduk->warna)
                • {{ $detail->detailProduk->warna->nama_warna }}
              @endif
            </p>
            @endif
          </div>

          <div class="text-right whitespace-nowrap">
            <p class="font-black text-[#63A2BB]">
              Rp {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}
            </p>
          </div>
        </div>
      @empty
        <p class="text-center text-gray-500 py-8">
          {{ __('ui.no_products_to_show') }}
        </p>
      @endforelse

@php
        $details = collect($pesanan->details ?? []);
        $totalDetail = $details->count();
        $totalBarang = (int) $details->sum('jumlah');
      @endphp

      @if($pesanan)
        <div class="pt-4 border-t-2 border-gray-100 mt-4">
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="font-bold text-gray-900">{{ __('ui.tracking_total_products') }}</span>
              <span class="text-xl font-black text-[#63A2BB]">{{ $totalBarang }} barang</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm font-semibold text-gray-700">{{ __('ui.tracking_item_lines') }}</span>
              <span class="text-sm font-bold text-gray-900">{{ $totalDetail }} baris</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
              <span class="font-bold text-gray-900">{{ __('ui.tracking_total_order') }}</span>
              <span class="text-2xl font-black text-[#63A2BB]">
                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection
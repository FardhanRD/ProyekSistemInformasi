@extends('layouts.buyer')

@section('title', __('ui.order_detail') . ' — MOVR')

@section('content')
@php
    $statusConfig = [
        'menunggu_pembayaran'     => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu Pembayaran'],
        'pembayaran_dikonfirmasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Pembayaran Dikonfirmasi'],
        'diproses'                => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Diproses'],
        'dikirim'                 => ['bg' => 'bg-[#63A2BB]/10', 'text' => 'text-[#63A2BB]', 'label' => 'Dikirim'],
        'selesai'                 => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Selesai'],
        'dibatalkan'              => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Dibatalkan'],
    ];
    $sc = $statusConfig[$transaksi->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => ucfirst($transaksi->status)];
    $trackingLogs = $transaksi->pesanan?->trackingLog ?? collect();
    $sudahRating = false;
    if ($transaksi->status === 'selesai' && auth()->user()?->buyer) {
        $sudahRating = \App\Models\RatingProduk::where('transaksi_id', $transaksi->transaksi_id)
            ->where('buyer_id', auth()->user()->buyer->buyer_id)
            ->exists();
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center gap-4 mb-6">
    <a href="{{ route('orders.index') }}"
       class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center hover:bg-gray-50 transition flex-shrink-0">
      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div class="flex-1">
      <h1 class="text-xl font-black text-gray-900">{{ __('ui.order_detail') }}</h1>
      <p class="text-sm text-gray-400 mt-0.5">{{ $transaksi->kode_transaksi }}</p>
    </div>
    <span class="text-xs font-bold px-4 py-2 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
      {{ $sc['label'] }}
    </span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
      @if($transaksi->status === 'menunggu_pembayaran')
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h3 class="font-bold text-amber-800 mb-1">Segera Lakukan Pembayaran</h3>
            <p class="text-sm text-amber-700">Waktu pembayaran untuk pesanan ini masih aktif.</p>
          </div>
          <a href="{{ route('payment.show', $transaksi->kode_transaksi) }}" class="bg-[#63A2BB] text-white px-6 py-3 rounded-2xl font-bold text-sm hover:bg-[#4A8BA3] transition shadow-sm inline-flex items-center justify-center">
            {{ __('ui.pay_now') }}
          </a>
        </div>
      @endif

      <div class="bg-white rounded-3xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
          <svg class="w-4 h-4 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          {{ __('ui.ordered_products') }}
        </h3>

        <div class="space-y-4">
          @foreach($transaksi->transaksiDetail as $item)
            @php
              $gambar = $item->detailProduk->produk->gambarUtama ?? null;
              $imgPath = $gambar?->url_safe ?? asset('images/placeholder.png');
              $warna = $item->warna_snap ?? optional($item->detailProduk->warna)->nama_warna ?? '-';
            @endphp
            <div class="flex gap-4 items-start p-4 rounded-2xl border border-gray-100 hover:bg-gray-50 transition">
              <img src="{{ $imgPath }}" alt="Product" class="w-16 h-16 rounded-2xl object-cover flex-shrink-0 bg-gray-50">
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-800 text-sm line-clamp-2">{{ $item->nama_produk_snap }}</h4>
                <div class="flex flex-wrap gap-2 mt-2">
                  @if($item->ukuran_snap)
                    <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Size: {{ $item->ukuran_snap }}</span>
                  @endif
                  @if($warna && $warna !== '-')
                    <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $warna }}</span>
                  @endif
                  <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">x{{ $item->quantity }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Rp {{ number_format($item->harga_snap, 0, ',', '.') }} / pcs</p>
              </div>
              <p class="font-bold text-gray-800 text-sm flex-shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white rounded-3xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
          <svg class="w-4 h-4 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          </svg>
          {{ __('ui.shipping_info') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-gray-50 rounded-2xl p-4">
            <p class="text-xs text-gray-400 mb-1">Penerima</p>
            <p class="font-semibold text-gray-800 text-sm">{{ $transaksi->alamat->nama_penerima ?? '-' }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $transaksi->alamat->no_telepon ?? '' }}</p>
          </div>
          <div class="bg-gray-50 rounded-2xl p-4">
            <p class="text-xs text-gray-400 mb-1">Ekspedisi</p>
            <p class="font-semibold text-gray-800 text-sm">
              {{ $transaksi->ekspedisi->nama_ekspedisi ?? '-' }}
              {{ $transaksi->ekspedisi->jenis_layanan ?? '' }}
            </p>
            @if($transaksi->pesanan?->no_resi)
              <p class="text-xs text-[#63A2BB] font-mono mt-1">{{ $transaksi->pesanan->no_resi }}</p>
            @endif
          </div>
          <div class="bg-gray-50 rounded-2xl p-4 md:col-span-2">
            <p class="text-xs text-gray-400 mb-1">Alamat Tujuan</p>
            <p class="font-medium text-gray-700 text-sm leading-relaxed">
              {{ $transaksi->alamat->alamat_lengkap ?? '-' }},
              {{ $transaksi->alamat->kecamatan ?? '' }},
              {{ $transaksi->alamat->kota ?? '' }},
              {{ $transaksi->alamat->provinsi ?? '' }}
              {{ $transaksi->alamat->kode_pos ?? '' }}
            </p>
          </div>
        </div>
      </div>

      @if($trackingLogs->isNotEmpty())
        <div class="bg-white rounded-3xl p-5 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            {{ __('ui.shipping_history') }}
          </h3>
          <div class="space-y-3">
            @foreach($trackingLogs->sortByDesc('waktu_update') as $log)
              <div class="flex gap-3">
                <div class="flex flex-col items-center">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $loop->first ? 'bg-[#63A2BB]' : 'bg-gray-100' }}">
                    @if($loop->first)
                      <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                      </svg>
                    @else
                      <div class="w-1.5 h-1.5 bg-gray-400 rounded-full"></div>
                    @endif
                  </div>
                  @if(!$loop->last)
                    <div class="w-px flex-1 bg-gray-100 my-1"></div>
                  @endif
                </div>
                <div class="pb-3">
                  <p class="text-sm font-semibold {{ $loop->first ? 'text-[#63A2BB]' : 'text-gray-700' }}">{{ $log->status }}</p>
                  @if($log->deskripsi)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $log->deskripsi }}</p>
                  @endif
                  @if($log->lokasi)
                    <p class="text-xs text-gray-400 mt-0.5">📍 {{ $log->lokasi }}</p>
                  @endif
                  <p class="text-[11px] text-gray-300 mt-1">{{ \Carbon\Carbon::parse($log->waktu_update)->isoFormat('D MMM YYYY, HH:mm') }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="space-y-5">
      <div class="bg-white rounded-3xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
          <svg class="w-4 h-4 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          {{ __('ui.summary_payment') }}
        </h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>{{ __('ui.cart_products_subtotal') }}</span>
            <span class="font-medium">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>{{ __('ui.cart_shipping_cost') }}</span>
            <span class="font-medium">Rp {{ number_format($transaksi->ongkos_kirim, 0, ',', '.') }}</span>
          </div>
          @if($transaksi->diskon_voucher > 0)
            <div class="flex justify-between text-green-600">
              <span>{{ __('ui.voucher_discount') }}</span>
              <span class="font-medium">-Rp {{ number_format($transaksi->diskon_voucher, 0, ',', '.') }}</span>
            </div>
          @endif
          <div class="border-t border-gray-100 pt-2 mt-2 flex justify-between">
            <span class="font-bold text-gray-800">{{ __('ui.total') }}</span>
            <span class="font-black text-[#63A2BB] text-base">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
          </div>
        </div>

        @if($transaksi->pembayaran)
          <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">{{ __('ui.payment_method') }}</p>
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-[#63A2BB]/10 rounded-lg flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[#63A2BB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
              </div>
              <span class="text-sm font-semibold text-gray-700">{{ $transaksi->pembayaran->metodePembayaran->metode ?? '-' }}</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
              @php
                $bayarStatus = [
                  'menunggu' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'label' => 'Menunggu'],
                  'berhasil' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'label' => 'Berhasil'],
                  'gagal' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'label' => 'Gagal'],
                  'menunggu_konfirmasi' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'label' => 'Menunggu Konfirmasi'],
                ];
                $bs = $bayarStatus[$transaksi->pembayaran->status_pembayaran] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'label' => ucfirst($transaksi->pembayaran->status_pembayaran ?? 'Menunggu')];
              @endphp
              <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $bs['bg'] }} {{ $bs['text'] }}">{{ $bs['label'] }}</span>
            </div>
          </div>
        @endif
      </div>

      <div class="bg-white rounded-3xl p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4 text-sm">{{ __('ui.order_info') }}</h3>
        <div class="space-y-3">
          <div>
            <p class="text-xs text-gray-400">{{ __('ui.order_code') }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <p class="font-mono font-semibold text-sm text-gray-700">{{ $transaksi->kode_transaksi }}</p>
              <button onclick="navigator.clipboard.writeText('{{ $transaksi->kode_transaksi }}').then(() => showToast('Kode disalin!'))"
                      class="text-[#63A2BB] hover:underline text-xs">
                {{ __('ui.copy') }}
              </button>
            </div>
          </div>
          <div>
            <p class="text-xs text-gray-400">{{ __('ui.order_date') }}</p>
            <p class="font-semibold text-sm text-gray-700 mt-0.5">
              {{ \Carbon\Carbon::parse($transaksi->tanggal)->isoFormat('D MMMM YYYY, HH:mm') }}
            </p>
          </div>
          @if($transaksi->catatan_buyer)
            <div>
              <p class="text-xs text-gray-400">{{ __('ui.note') }}</p>
              <p class="text-sm text-gray-600 mt-0.5">{{ $transaksi->catatan_buyer }}</p>
            </div>
          @endif
        </div>
      </div>

      <div class="space-y-2">
        @if($transaksi->status === 'menunggu_pembayaran')
          <a href="{{ route('payment.show', $transaksi->kode_transaksi) }}"
             class="w-full flex items-center justify-center gap-2 bg-[#63A2BB] text-white py-3.5 rounded-2xl font-bold text-sm hover:bg-[#4A8BA3] hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            {{ __('ui.pay_now') }}
          </a>
        @elseif(in_array($transaksi->status, ['diproses', 'dikirim']))
          <a href="{{ route('tracking.show', $transaksi->kode_transaksi) }}"
             class="w-full flex items-center justify-center gap-2 border-2 border-[#63A2BB] text-[#63A2BB] py-3.5 rounded-2xl font-bold text-sm hover:bg-[#63A2BB] hover:text-white transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            {{ __('ui.track_package') }}
          </a>
        @elseif($transaksi->status === 'selesai')
          @if(!$sudahRating)
            <a href="{{ route('orders.rating', $transaksi->kode_transaksi) }}"
               class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white py-3.5 rounded-2xl font-bold text-sm hover:bg-amber-600 hover:-translate-y-0.5 transition-all">
              ⭐ {{ __('ui.write_review') }}
            </a>
          @endif
        @endif

        <a href="{{ route('orders.index') }}"
           class="w-full flex items-center justify-center text-gray-500 py-3 rounded-2xl text-sm font-medium hover:text-[#63A2BB] transition">
          ← {{ __('ui.back_to_orders') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
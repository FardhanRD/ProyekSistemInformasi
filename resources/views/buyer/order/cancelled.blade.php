@extends('layouts.buyer')
@section('title', 'Pesanan Dibatalkan — MOVR')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
  <div class="w-full max-w-md text-center">
    <div class="relative w-28 h-28 mx-auto mb-6">
      <div class="w-28 h-28 bg-red-50 rounded-full flex items-center justify-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </div>
      </div>
    </div>

    <h1 class="text-2xl font-black text-gray-900 mb-2">Pesanan Dibatalkan</h1>
    <p class="text-gray-500 mb-2">Pesanan kamu telah berhasil dibatalkan</p>
    <p class="font-mono font-bold text-gray-700 bg-gray-100 px-4 py-2 rounded-xl inline-block mb-6">
      {{ $kode_transaksi ?? '-' }}
    </p>

    <div class="bg-white rounded-2xl p-5 shadow-sm text-left mb-6 space-y-3">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="font-semibold text-gray-700 text-sm">Stok produk dikembalikan</p>
          <p class="text-xs text-gray-400 mt-0.5">Produk yang kamu pesan sudah tersedia kembali</p>
        </div>
      </div>

      <div class="flex items-start gap-3">
        <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="font-semibold text-gray-700 text-sm">Tidak ada biaya pembatalan</p>
          <p class="text-xs text-gray-400 mt-0.5">Pembatalan sebelum pembayaran tidak dikenakan biaya</p>
        </div>
      </div>

      <div class="flex items-start gap-3">
        <div class="w-8 h-8 bg-amber-50 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </div>
        <div>
          <p class="font-semibold text-gray-700 text-sm">Belanja lagi kapan saja</p>
          <p class="text-xs text-gray-400 mt-0.5">Produk favorit kamu masih tersedia di MOVR</p>
        </div>
      </div>
    </div>

    @if(isset($transaksi) && $transaksi->transaksiDetail->isNotEmpty())
    <div class="bg-white rounded-2xl p-4 shadow-sm text-left mb-6">
      <p class="text-xs font-bold text-gray-400 uppercase mb-3">Produk yang dibatalkan</p>
      @foreach($transaksi->transaksiDetail as $item)
      <div class="flex items-center gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
        <img src="{{ $item->detailProduk->produk->gambarUtama?->url_safe ?? asset('images/placeholder.png') }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 opacity-50 bg-gray-100">
        <div class="flex-1">
          <p class="text-sm font-semibold text-gray-500 line-clamp-1">{{ $item->nama_produk_snap }}</p>
          <p class="text-xs text-gray-400">{{ $item->ukuran_snap }} · x{{ $item->quantity }}</p>
        </div>
        <p class="text-sm text-gray-400 line-through">Rp {{ number_format($item->subtotal,0,',','.') }}</p>
      </div>
      @endforeach
    </div>
    @endif

    <div class="space-y-3">
      <a href="/" class="w-full flex items-center justify-center gap-2 bg-[#63A2BB] text-white py-4 rounded-2xl font-bold text-sm hover:bg-[#4A8BA3] hover:-translate-y-0.5 hover:shadow-lg hover:shadow-[#63A2BB]/30 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        Belanja Lagi
      </a>
      <a href="{{ route('orders.index', ['status' => 'dibatalkan']) }}" class="w-full flex items-center justify-center border-2 border-gray-200 text-gray-500 py-3.5 rounded-2xl font-semibold text-sm hover:border-[#63A2BB] hover:text-[#63A2BB] transition-all duration-200">
        Lihat Riwayat Pesanan
      </a>
    </div>
  </div>
</div>
@endsection
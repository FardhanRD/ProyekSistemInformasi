@extends('layouts.buyer')
@section('title', 'Pesanan Saya — MOVR')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
  {{-- Header --}}
  <div class="mb-8">
    <h1 class="text-3xl font-black text-gray-900">
      Pesanan Saya
    </h1>
    <p class="text-gray-500 text-sm mt-1">
      Kelola dan pantau semua pesanan kamu
    </p>
  </div>

  {{-- Filter Tabs --}}
  <div class="flex gap-2 overflow-x-auto pb-4 mb-8">
    @php
      $tabs = [
        'semua' => 'Semua',
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan'
      ];
      $currentStatus = request('status', 'semua');
    @endphp
    @foreach($tabs as $key => $label)
      <a href="{{ route('order.index', ['status' => $key]) }}"
         class="px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap 
                transition-all {{ $currentStatus === $key 
                  ? 'bg-[#63A2BB] text-white' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  {{-- Orders List --}}
  @if($orders->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
      <div class="w-24 h-24 bg-gray-100 rounded-full 
                  flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-gray-400" 
             fill="none" stroke="currentColor" 
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" 
                stroke-width="1.5"
                d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10M8 5v10m8-10v10"/>
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-700 mb-2">
        Belum ada pesanan
      </h2>
      <p class="text-gray-400 mb-6">
        Mulai belanja sekarang untuk mendapatkan produk favorit
      </p>
      <a href="{{ route('home') }}" class="btn-primary">
        Jelajahi Produk
      </a>
    </div>
  @else
    <div class="space-y-4">
      @foreach($orders as $order)
        @php
          $statusColors = [
            'menunggu_pembayaran' => 'bg-amber-50 border-amber-200',
            'diproses' => 'bg-blue-50 border-blue-200',
            'dikirim' => 'bg-purple-50 border-purple-200',
            'selesai' => 'bg-green-50 border-green-200',
            'dibatalkan' => 'bg-red-50 border-red-200'
          ];
          $statusBadgeColors = [
            'menunggu_pembayaran' => 'bg-amber-100 text-amber-700',
            'diproses' => 'bg-blue-100 text-blue-700',
            'dikirim' => 'bg-purple-100 text-purple-700',
            'selesai' => 'bg-green-100 text-green-700',
            'dibatalkan' => 'bg-red-100 text-red-700'
          ];
          $statusLabels = [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'diproses' => 'Sedang Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
          ];
          $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
          $firstItem = $order->details->first();
        @endphp
        <div class="bg-white rounded-2xl border-2 {{ $statusColors[$order->status] ?? 'border-gray-200' }} 
                    p-5 hover:shadow-lg transition-shadow">
          {{-- Header --}}
          <div class="flex items-start justify-between mb-4 pb-4 border-b-2 border-gray-100">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">
                  {{ $order->kode_transaksi }}
                </h3>
              </div>
              <p class="text-xs text-gray-400">
                {{ $order->created_at->locale('id')->format('d M Y H:i') }}
              </p>
            </div>
            <span class="px-3 py-1.5 {{ $statusBadgeColors[$order->status] ?? 'bg-gray-100 text-gray-700' }} 
                         text-xs font-bold rounded-full">
              {{ $statusLabel }}
            </span>
          </div>

          {{-- Items Preview --}}
          @if($firstItem)
          <div class="flex gap-4 mb-4">
            @php
              $img = $firstItem->detailProduk->produk->gambarUtama;
            @endphp
            <img src="{{ $img?->url_safe ?? 'https://via.placeholder.com/80' }}"
                 alt="{{ $firstItem->detailProduk->produk->nama_produk }}"
                 class="w-20 h-20 rounded-lg object-cover bg-gray-100">
            
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-gray-900 line-clamp-1 mb-1">
                {{ $firstItem->detailProduk->produk->nama_produk }}
              </h4>
              <p class="text-xs text-gray-500 mb-2">
                {{ $firstItem->jumlah }}x @ Rp {{ number_format($firstItem->harga_satuan, 0, ',', '.') }}
              </p>
              @if($order->details->count() > 1)
                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                  +{{ $order->details->count() - 1 }} produk lain
                </span>
              @endif
            </div>

            <div class="text-right whitespace-nowrap">
              <p class="text-xs text-gray-500 mb-1">Total</p>
              <p class="text-lg font-black text-[#63A2BB]">
                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
              </p>
            </div>
          </div>
          @endif

          {{-- Actions --}}
          <div class="flex gap-2 pt-4 border-t-2 border-gray-100">
            @if($order->status === 'menunggu_pembayaran')
              <a href="{{ route('payment.show', $order->kode_transaksi) }}"
                 class="flex-1 bg-amber-500 text-white py-2 rounded-lg 
                        text-center font-bold text-sm hover:bg-amber-600 transition">
                Bayar
              </a>
            @elseif($order->status === 'dikirim')
              <a href="{{ route('tracking.show', $order->kode_transaksi) }}"
                 class="flex-1 bg-[#63A2BB] text-white py-2 rounded-lg 
                        text-center font-bold text-sm hover:shadow-lg transition">
                Lacak Paket
              </a>
            @elseif($order->status === 'selesai')
              <a href="{{ route('order.show', $order->kode_transaksi) }}"
                 class="flex-1 bg-[#63A2BB] text-white py-2 rounded-lg 
                        text-center font-bold text-sm hover:shadow-lg transition">
                Beri Ulasan
              </a>
              <a href="{{ route('product.show', $firstItem->detailProduk->produk->slug ?? '#') }}"
                 class="flex-1 border-2 border-[#63A2BB] text-[#63A2BB] py-2 rounded-lg 
                        text-center font-bold text-sm hover:bg-[#63A2BB]/5 transition">
                Beli Lagi
              </a>
            @endif
            
            <a href="{{ route('order.show', $order->kode_transaksi) }}"
               class="px-4 py-2 border-2 border-gray-200 text-gray-700 rounded-lg 
                      text-center font-bold text-sm hover:border-[#63A2BB] 
                      hover:text-[#63A2BB] transition">
              Detail
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

@endsection

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
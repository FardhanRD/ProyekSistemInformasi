@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Pesanan Saya</h1>

        <!-- Filters & Search -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
            <form action="{{ route('order.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="flex-1">
                    <label class="sr-only">Cari Kode Transaksi</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <span class="text-gray-500">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm font-medium">Filter</button>
                    @if(request()->hasAny(['search', 'start_date', 'end_date']))
                        <a href="{{ route('order.index', ['status' => $currentStatus]) }}" class="text-blue-600 text-sm font-medium hover:underline">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabs Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 overflow-x-auto">
            <div class="flex min-w-max border-b">
                @php
                    $tabs = [
                        'all' => 'Semua',
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'diproses' => 'Diproses',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan'
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                    <a href="{{ route('order.index', array_merge(request()->except('status'), ['status' => $key])) }}" 
                       class="px-6 py-3 font-medium text-sm border-b-2 transition-colors whitespace-nowrap {{ $currentStatus === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-4">
            @forelse($orders as $order)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <!-- Header Card -->
                    <div class="flex justify-between items-start border-b pb-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-800">{{ $order->kode_transaksi }}</span>
                            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        <div>
                            @php
                                $statusColors = [
                                    'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-800',
                                    'pembayaran_dikonfirmasi' => 'bg-blue-100 text-blue-800',
                                    'diproses' => 'bg-blue-100 text-blue-800',
                                    'dikirim' => 'bg-purple-100 text-purple-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'dibatalkan' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'menunggu_pembayaran' => 'Menunggu Pembayaran',
                                    'pembayaran_dikonfirmasi' => 'Diproses',
                                    'diproses' => 'Diproses',
                                    'dikirim' => 'Dikirim',
                                    'selesai' => 'Selesai',
                                    'dibatalkan' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Detail Items -->
                    @php $firstItem = $order->details->first(); @endphp
                    @if($firstItem)
                        <div class="flex gap-4 items-center">
                            @php
                                $img = $firstItem->detailProduk->produk->images->first();
                                $imgPath = $img ? asset('storage/'.$img->gambar_url) : 'https://placehold.co/100x100?text=No+Image';
                            @endphp
                            <img src="{{ $imgPath }}" alt="Product" class="w-20 h-20 object-cover rounded-md border border-gray-200">
                            
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 line-clamp-1">{{ $firstItem->detailProduk->produk->nama_produk }}</h3>
                                <p class="text-sm text-gray-500">{{ $firstItem->quantity }} barang x Rp {{ number_format($firstItem->harga_satuan, 0, ',', '.') }}</p>
                                @if($order->details->count() > 1)
                                    <p class="text-xs text-gray-400 mt-1">+ {{ $order->details->count() - 1 }} produk lainnya</p>
                                @endif
                            </div>
                            
                            <div class="text-right border-l pl-4 hidden md:block">
                                <p class="text-sm text-gray-500 mb-1">Total Belanja</p>
                                <p class="font-bold text-lg text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Mobile Total Belanja -->
                    <div class="md:hidden mt-4 pt-4 border-t flex justify-between items-center">
                        <p class="text-sm text-gray-500">Total Belanja</p>
                        <p class="font-bold text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-4 border-t flex flex-wrap justify-end gap-2">
                        @if($order->status === 'menunggu_pembayaran')
                            <a href="{{ route('payment.show', $order->kode_transaksi) }}" class="bg-orange-600 text-white text-sm font-medium py-2 px-4 rounded hover:bg-orange-700 transition">Bayar Sekarang</a>
                        @elseif($order->status === 'dikirim')
                            <a href="{{ route('tracking.show', $order->kode_transaksi) }}" class="bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded hover:bg-blue-700 transition">Lacak Paket</a>
                            <form action="{{ route('order.show', $order->kode_transaksi) }}" method="GET">
                                <!-- Dummy complete order action if needed -->
                                <button type="button" onclick="alert('Fitur selesaikan pesanan (Pesanan Diterima) dapat diimplementasikan di sini.')" class="bg-green-600 text-white text-sm font-medium py-2 px-4 rounded hover:bg-green-700 transition">Pesanan Diterima</button>
                            </form>
                        @elseif($order->status === 'selesai')
                            <!-- Tombol Beri Ulasan bisa ditampilkan jika logic pengecekan blm direview diterapkan di view -->
                            <a href="{{ route('order.show', $order->kode_transaksi) }}#ulasan" class="bg-yellow-500 text-white text-sm font-medium py-2 px-4 rounded hover:bg-yellow-600 transition">Beri Ulasan</a>
                            <a href="{{ route('product.show', $firstItem->detailProduk->produk->slug ?? '#') }}" class="bg-blue-50 text-blue-600 border border-blue-200 text-sm font-medium py-2 px-4 rounded hover:bg-blue-100 transition">Beli Lagi</a>
                        @elseif(in_array($order->status, ['pembayaran_dikonfirmasi', 'diproses']))
                            <a href="{{ route('tracking.show', $order->kode_transaksi) }}" class="bg-blue-50 text-blue-600 border border-blue-200 text-sm font-medium py-2 px-4 rounded hover:bg-blue-100 transition">Lacak Pesanan</a>
                        @endif
                        
                        <a href="{{ route('order.show', $order->kode_transaksi) }}" class="border border-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded hover:bg-gray-50 transition">Detail Pesanan</a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-lg shadow-sm border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pesanan</h3>
                    <p class="text-gray-500">Anda belum memiliki pesanan dengan status ini.</p>
                    <a href="{{ route('product.index') }}" class="mt-4 inline-block bg-blue-600 text-white font-medium py-2 px-6 rounded hover:bg-blue-700 transition">Mulai Belanja</a>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
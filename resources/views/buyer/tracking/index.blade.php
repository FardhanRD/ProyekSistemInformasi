@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center text-sm text-gray-500">
            <a href="{{ route('order.index') }}" class="hover:text-blue-600 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pesanan
            </a>
            <span class="mx-2">/</span>
            <span>Lacak Pesanan</span>
        </div>

        <h1 class="text-2xl font-bold mb-6 text-gray-800">Lacak Pengiriman</h1>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
            <!-- Header Info -->
            <div class="p-6 border-b bg-gray-50 flex flex-wrap justify-between items-start gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Kurir Pengiriman</p>
                    <p class="font-bold text-lg">{{ $ekspedisi->nama_ekspedisi ?? 'Kurir' }} ({{ $ekspedisi->jenis_layanan ?? '-' }})</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-medium text-blue-600">{{ $pesanan->no_resi ?? 'Belum ada resi' }}</span>
                        @if($pesanan && $pesanan->no_resi)
                        <button onclick="navigator.clipboard.writeText('{{ $pesanan->no_resi }}').then(() => alert('Resi disalin!'))" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 mb-1">Estimasi Tiba</p>
                    <p class="font-bold text-gray-800">{{ $pesanan && $pesanan->estimasi_tiba ? \Carbon\Carbon::parse($pesanan->estimasi_tiba)->translatedFormat('d M Y') : '-' }}</p>
                    @php
                        $statusColors = [
                            'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-800',
                            'diproses' => 'bg-blue-100 text-blue-800',
                            'dikirim' => 'bg-purple-100 text-purple-800',
                            'selesai' => 'bg-green-100 text-green-800',
                        ];
                        $pesananStatus = $pesanan ? $pesanan->status_pesanan : 'menunggu_konfirmasi';
                    @endphp
                    <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$pesananStatus] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $pesananStatus)) }}
                    </span>
                </div>
            </div>

            <!-- Alamat Tujuan -->
            <div class="p-6 border-b">
                <div class="flex items-start gap-3">
                    <div class="mt-1 flex-shrink-0 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 mb-1">Alamat Tujuan</p>
                        <p class="text-sm text-gray-600">{{ $alamatTujuan->nama_penerima ?? '-' }} ({{ $alamatTujuan->no_telepon ?? '-' }})</p>
                        <p class="text-sm text-gray-600">{{ $alamatTujuan->alamat_lengkap ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Timeline Tracking -->
            <div class="p-6">
                <h3 class="font-bold text-gray-800 mb-6">Status Pengiriman</h3>
                
                @if($trackingLogs->count() > 0)
                    <div class="relative border-l-2 border-gray-200 ml-3 md:ml-4">
                        @foreach($trackingLogs as $index => $log)
                            <div class="mb-8 ml-6 relative">
                                <!-- Dot Marker -->
                                <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-9 ring-4 ring-white {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                    @if($index === 0 && $pesananStatus === 'selesai')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    @elseif($index === 0)
                                        <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                    @endif
                                </span>
                                
                                <!-- Content -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-4">
                                    <div>
                                        <h4 class="font-bold {{ $index === 0 ? 'text-blue-600' : 'text-gray-800' }}">{{ $log->status }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $log->deskripsi }}</p>
                                        @if($log->lokasi)
                                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                {{ $log->lokasi }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500 sm:text-right whitespace-nowrap">
                                        <p>{{ \Carbon\Carbon::parse($log->waktu_update)->translatedFormat('d M Y') }}</p>
                                        <p>{{ \Carbon\Carbon::parse($log->waktu_update)->translatedFormat('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Belum ada pembaruan status pengiriman.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ringkasan Produk -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800">Ringkasan Produk</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($transaksi->details as $detail)
                    <div class="flex gap-4 items-center">
                        @php
                            $img = $detail->detailProduk->produk->images->first();
                            $imgPath = $img ? asset('storage/'.$img->gambar_url) : 'https://placehold.co/100x100?text=No+Image';
                        @endphp
                        <img src="{{ $imgPath }}" alt="Product" class="w-16 h-16 object-cover rounded-md border border-gray-200">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 line-clamp-1">{{ $detail->detailProduk->produk->nama_produk }}</h4>
                            <p class="text-sm text-gray-500">{{ $detail->quantity }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
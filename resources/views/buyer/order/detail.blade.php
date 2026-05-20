@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center text-sm text-gray-500">
            <a href="{{ route('orders.index') }}" class="hover:text-blue-600 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pesanan
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
            <!-- Header -->
            <div class="p-6 border-b flex flex-wrap justify-between items-center gap-4 bg-gray-50">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Detail Pesanan</h1>
                    <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                        <span>Kode: <span class="font-semibold text-gray-700">{{ $transaksi->kode_transaksi }}</span></span>
                        <span class="text-gray-300">|</span>
                        <span>{{ \Carbon\Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y, H:i') }}</span>
                    </div>
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
                    @endphp
                    <span class="px-4 py-1.5 text-sm font-semibold rounded-full {{ $statusColors[$transaksi->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $transaksi->status)) }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                @if($transaksi->status === 'menunggu_pembayaran')
                    <div class="mb-8 bg-orange-50 border border-orange-200 rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-orange-800 mb-1">Segera Lakukan Pembayaran</h3>
                            <p class="text-sm text-orange-700">Waktu pembayaran Anda akan segera habis.</p>
                        </div>
                        <a href="{{ route('payment.show', $transaksi->kode_transaksi) }}" class="bg-orange-600 text-white px-6 py-2 rounded-md font-medium hover:bg-orange-700 transition shadow-sm">Bayar Sekarang</a>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Info Pengiriman -->
                    <div>
                        <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Informasi Pengiriman</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500 block">Kurir</span>
                                <span class="font-medium">{{ $transaksi->ekspedisi->nama_ekspedisi ?? '-' }} ({{ $transaksi->ekspedisi->jenis_layanan ?? '-' }})</span>
                            </div>
                            @if($transaksi->pesanan && $transaksi->pesanan->no_resi)
                            <div>
                                <span class="text-gray-500 block">No. Resi</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-blue-600">{{ $transaksi->pesanan->no_resi }}</span>
                                    <a href="{{ route('tracking.show', $transaksi->kode_transaksi) }}" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded transition">Lacak</a>
                                </div>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-500 block">Alamat Tujuan</span>
                                <p class="font-medium">{{ $transaksi->alamat->nama_penerima ?? '-' }}</p>
                                <p class="text-gray-600 mt-1">
                                    {{ $transaksi->alamat->no_telepon ?? '-' }}<br>
                                    {{ $transaksi->alamat->alamat_lengkap ?? '-' }}<br>
                                    {{ $transaksi->alamat->kelurahan ?? '' }}, {{ $transaksi->alamat->kecamatan ?? '' }}, {{ $transaksi->alamat->kota ?? '' }}, {{ $transaksi->alamat->provinsi ?? '' }} {{ $transaksi->alamat->kode_pos ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Pembayaran -->
                    <div>
                        <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Informasi Pembayaran</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500 block">Metode Pembayaran</span>
                                <span class="font-medium">{{ $transaksi->pembayaran->metode->nama_metode ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Status Pembayaran</span>
                                <span class="font-medium">{{ ucfirst($transaksi->pembayaran->status_pembayaran ?? 'Menunggu') }}</span>
                            </div>
                            <div class="pt-2 border-t mt-4 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Harga ({{ $transaksi->details->sum('quantity') }} Barang)</span>
                                    <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Ongkos Kirim</span>
                                    <span>Rp {{ number_format($transaksi->ongkos_kirim, 0, ',', '.') }}</span>
                                </div>
                                @if($transaksi->diskon_voucher > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Diskon Voucher</span>
                                    <span>- Rp {{ number_format($transaksi->diskon_voucher, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between font-bold text-base pt-2 border-t mt-2">
                                    <span>Total Belanja</span>
                                    <span class="text-orange-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Produk -->
                <div>
                    <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Detail Produk</h3>
                    <div class="space-y-4">
                        @foreach($transaksi->details as $detail)
                            <div class="flex gap-4 items-start p-4 border rounded-lg hover:bg-gray-50 transition">
                                @php
                                    $img = $detail->detailProduk->produk->images->first();
                                    $imgPath = $img ? asset('storage/'.$img->gambar_url) : 'https://placehold.co/100x100?text=No+Image';
                                @endphp
                                <img src="{{ $imgPath }}" alt="Product" class="w-20 h-20 object-cover rounded-md border border-gray-200">
                                
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800">{{ $detail->detailProduk->produk->nama_produk }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">Variasi: {{ $detail->detailProduk->warna ?? '-' }}, Ukuran: {{ $detail->detailProduk->ukuran ?? '-' }}</p>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-sm font-medium">{{ $detail->quantity }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                                        <p class="font-bold">Rp {{ number_format($detail->harga_satuan * $detail->quantity, 0, ',', '.') }}</p>
                                    </div>
                                    
                                    @if($transaksi->status === 'selesai' && !in_array($detail->detailProduk->produk_id, $reviewedProductIds))
                                        <div class="mt-3 text-right">
                                            <button type="button" class="text-sm bg-yellow-50 text-yellow-600 border border-yellow-200 px-3 py-1.5 rounded hover:bg-yellow-100 transition"
                                                onclick="alert('Formulir ulasan untuk produk ini dapat dipanggil di sini via Modal.')">
                                                Tulis Ulasan
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
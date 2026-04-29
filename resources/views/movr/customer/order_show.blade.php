@extends('movr.layouts.app')

@section('content')
<!-- Dashboard Header -->
<section class="py-6 bg-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-black">Detail Pesanan #{{ $order->midtrans_order_id ?? $order->id }}</h1>
        <p class="text-gray-600">Informasi lengkap pesanan Anda</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-black mb-4">Item dalam Pesanan</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden mr-4">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-500"><i class="fas fa-image"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-medium text-black">{{ $item->product->name ?? 'Produk Tidak Ditemukan' }}</h3>
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}/unit</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-black">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-black mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Pesanan:</span>
                            <span class="text-black">#{{ $order->midtrans_order_id ?? $order->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="text-black">{{ $order->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="@if($order->status == 'paid') text-green-600
                                         @elseif($order->status == 'pending') text-yellow-600
                                         @elseif($order->status == 'cancelled') text-blue-600
                                         @elseif($order->status == 'processing') text-blue-600
                                         @elseif($order->status == 'shipped') text-indigo-600
                                         @elseif($order->status == 'delivered') text-purple-600
                                         @else text-gray-600 @endif font-medium">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran:</span>
                            <span class="text-black">{{ $order->payment_method ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-black mb-4">Rincian Pembayaran</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal Produk:</span>
                            <span class="text-black">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim:</span>
                            <span class="text-black">Rp 15.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Biaya Layanan:</span>
                            <span class="text-black">Rp 1.000</span>
                        </div>
                        <div class="border-t border-gray-300 pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="text-black font-bold">Total:</span>
                                <span class="text-accent-green font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($order->payment_url && in_array($order->status, ['pending', 'processing']))
                <a href="{{ $order->payment_url }}" class="w-full bg-accent-green text-white py-3 px-4 rounded-lg font-medium hover:bg-accent-green/90 transition block text-center">
                    Bayar Sekarang
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
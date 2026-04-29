@extends('movr.layouts.app')

@section('content')
<section class="py-12 bg-white min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-300 rounded-xl p-8 shadow-lg text-center">
            @if($order->isPaid())
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-500 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-black mb-2">Pembayaran Berhasil!</h1>
                <p class="text-gray-600 mb-6">Terima kasih, pesanan Anda telah diproses.</p>
            @elseif($order->isPending())
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-black mb-2">Pembayaran Pending</h1>
                <p class="text-gray-600 mb-6">Pembayaran Anda sedang diproses.</p>
            @else
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-times text-blue-500 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-black mb-2">Pembayaran Gagal</h1>
                <p class="text-gray-600 mb-6">Terjadi masalah dengan pembayaran Anda.</p>
            @endif

            <div class="bg-gray-50 rounded-lg p-4 text-left mb-6">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-gray-600">Order ID:</div>
                    <div class="text-black font-medium">{{ $order->midtrans_order_id }}</div>
                    
                    <div class="text-gray-600">Status:</div>
                    <div class="text-black font-medium">
                        @if($order->isPaid())
                            <span class="text-green-600">Lunas</span>
                        @elseif($order->isPending())
                            <span class="text-yellow-600">Pending</span>
                        @else
                            <span class="text-blue-600">Gagal</span>
                        @endif
                    </div>
                    
                    <div class="text-gray-600">Total:</div>
                    <div class="text-black font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" class="flex-1 bg-accent-green text-white py-3 px-4 rounded-lg font-medium hover:bg-accent-green/90 transition">
                    Lanjut Belanja
                </a>
                <a href="{{ route('profil.index') }}" class="flex-1 bg-white border border-gray-300 text-black py-3 px-4 rounded-lg font-medium hover:bg-gray-100 transition">
                    Lihat Pesanan
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
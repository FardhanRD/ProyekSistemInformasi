@extends('movr.layouts.admin')

@section('content')
<section class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan #{{ $order->midtrans_order_id ?? $order->id }}</h1>
        <p class="text-gray-500">Informasi lengkap pesanan pelanggan</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Item Pesanan</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-xl overflow-hidden mr-4">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-500"><i class="fas fa-image"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item->product->name ?? 'Produk Tidak Ditemukan' }}</h3>
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div>
                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Pesanan</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID Pesanan:</span>
                            <span class="text-gray-900">#{{ $order->midtrans_order_id ?? $order->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal:</span>
                            <span class="text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
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
                            <span class="text-gray-500">Metode Pembayaran:</span>
                            <span class="text-gray-900">{{ $order->payment_method ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pelanggan:</span>
                            <span class="text-gray-900">{{ $order->user->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Email:</span>
                            <span class="text-gray-900">{{ $order->user->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Total</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal:</span>
                            <span class="text-gray-900">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ongkos Kirim:</span>
                            <span class="text-gray-900">Rp 15.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Biaya Layanan:</span>
                            <span class="text-gray-900">Rp 1.000</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-900 font-bold">Total:</span>
                                <span class="text-accent-green font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Update Form -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Ubah Status</h2>
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                            <select name="status" id="status" class="w-full border border-gray-200 rounded-xl px-3 py-2 bg-gray-50">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-accent-green text-black py-2 px-4 rounded-xl font-medium hover:bg-accent-green/90 transition">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
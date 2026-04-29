@extends('movr.layouts.admin')

@section('content')
<section class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Pesanan</h1>
        <p class="text-gray-500">Kelola pesanan pelanggan</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">ID Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Metode Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->midtrans_order_id ?? $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($order->status == 'paid') bg-green-100 text-green-700
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'cancelled') bg-blue-100 text-blue-700
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                                    @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-700
                                    @elseif($order->status == 'delivered') bg-purple-100 text-purple-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->payment_method ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-accent-green hover:text-green-700 mr-3">Lihat</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-600">
                                Tidak ada pesanan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
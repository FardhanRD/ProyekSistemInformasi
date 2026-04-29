@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-movr-black">Dashboard Inventori</h2>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-[#E5E5E5] bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Total Varian Terlacak</p>
            <p class="mt-2 text-3xl font-bold text-movr-black">{{ $total_variants_tracked ?? 0 }}</p>
        </div>
        <div class="rounded-lg border border-[#E5E5E5] bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
            <p class="mt-2 text-3xl font-bold text-movr-red">{{ $low_stock_count ?? 0 }}</p>
        </div>
    </div>

    <div class="rounded-lg border border-[#E5E5E5] bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-movr-black">Produk Stok Rendah</h3>
        <div class="overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-movr-black">Produk</th>
                        <th class="px-6 py-3 text-right font-semibold text-movr-black">Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @if(isset($low_stock_items) && count($low_stock_items) > 0)
                        @foreach($low_stock_items as $item)
                        <tr class="hover:bg-[#FAFAFA]">
                            <td class="px-6 py-4">{{ $item['product_name'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-movr-red">{{ $item['current_stock'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">Tidak ada produk dengan stok rendah</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

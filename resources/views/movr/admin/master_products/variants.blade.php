@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-movr-black">Varian Produk</h2>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-movr-red px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-movr-red/90">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Varian
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">ID</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Produk</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">SKU</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Atribut</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $variant)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4">{{ $variant['id'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $variant['product_name'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $variant['sku'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs">{{ $variant['attributes'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-sm text-movr-red hover:underline">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada varian produk</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

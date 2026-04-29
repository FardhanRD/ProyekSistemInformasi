@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-movr-black">Stock Movement</h2>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-movr-red px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-movr-red/90">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Movement
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Produk</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Tipe</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Jumlah</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $movement)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4">{{ isset($movement['created_at']) ? date('d/m/Y', strtotime($movement['created_at'])) : '-' }}</td>
                        <td class="px-6 py-4">{{ $movement['product_name'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded px-2 py-1 text-xs font-semibold {{ $movement['type'] === 'in' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $movement['type'] === 'in' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">{{ $movement['quantity'] ?? 0 }}</td>
                        <td class="px-6 py-4 text-xs">{{ $movement['notes'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada movement</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

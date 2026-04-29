@extends('movr.layouts.admin')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

<style>
    .cat-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .cat-scroll::-webkit-scrollbar-thumb {
        background: #bdc4d1;
        border-radius: 99px;
    }
</style>

<section class="space-y-3 text-[#273650]">
    <div>
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 rounded-md border border-[#e1e6ef] bg-white px-3 py-1.5 text-xs font-bold text-[#5d6a80] hover:bg-[#f7f9fc]">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <article class="overflow-hidden rounded-xl border border-[#e5e8ef] bg-white">
        <div class="flex flex-wrap items-center justify-end gap-2 border-b border-[#e6e9f0] p-3.5">
            <form method="GET" action="{{ route('admin.kategori.show', ['kategori' => $kategori->id]) }}" class="flex items-center gap-2">
                <input type="hidden" name="back" value="{{ $backUrl }}">
                <label class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-2.5 top-2.5 text-[11px] text-[#b3bbca]"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Produk..." class="h-8 rounded-md border border-[#e3e7ef] bg-white pl-8 pr-3 text-xs font-semibold text-[#51607a] focus:outline-none">
                </label>
                <button type="submit" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-[#e3e7ef] bg-white px-3 text-xs font-bold text-[#66758f] hover:bg-[#f7f9fc]">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filter
                </button>
            </form>
        </div>

        <div class="cat-scroll overflow-x-auto">
            <table class="min-w-[1080px] w-full">
                <thead class="border-b border-[#edf0f6] bg-[#fcfdff] text-[10px] uppercase tracking-[0.08em] text-[#b2baca]">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Gambar</th>
                        <th class="px-3 py-2 text-left">Nama Produk</th>
                        <th class="px-3 py-2 text-left">Supplier</th>
                        <th class="px-3 py-2 text-left">Harga</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf0f6] text-sm">
                    @forelse($products as $index => $produk)
                        @php
                            $rowNo = ($products->firstItem() ?? 1) + $index;
                            $available = $produk->stock > 0;
                        @endphp
                        <tr class="hover:bg-[#fafbfd]">
                            <td class="px-3 py-2.5 text-[11px] font-bold text-[#90a0b8]">#PRD-{{ str_pad((string) $rowNo, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-3 py-2.5">
                                <div class="h-10 w-10 overflow-hidden rounded-md border border-[#e2e6ee] bg-[#14171c]">
                                    @if($produk->image)
                                        <img src="{{ asset('storage/' . $produk->image) }}" alt="{{ $produk->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-[#737f95]"><i class="fa-regular fa-image text-xs"></i></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <p class="font-extrabold text-[#2a3a52]">{{ $produk->name }}</p>
                                <p class="truncate text-[11px] font-semibold text-[#97a2b5]">{{ \Illuminate\Support\Str::limit($produk->description, 44) }}</p>
                            </td>
                            <td class="px-3 py-2.5 text-sm font-bold text-[#55647b]">{{ $produk->supplier->store_name ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-sm font-extrabold text-[#23354d]">{{ $formatRupiah($produk->price) }}</td>
                            <td class="px-3 py-2.5">
                                <span class="text-[11px] font-extrabold uppercase {{ $available ? 'text-[#08a65a]' : 'text-[#d12a34]' }}">{{ $available ? 'Tersedia' : 'Stok Habis' }}</span>
                            </td>
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.produk.show', $produk->id) }}" class="text-[#70809a] hover:text-[#2f415f]" title="Lihat Detail"><i class="fa-regular fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm font-semibold text-[#95a0b5]">Belum ada data produk untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e9f0] bg-[#fbfcff] px-3.5 py-2.5">
            <p class="text-xs font-semibold text-[#96a0b2]">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items</p>
            <div class="flex items-center gap-1">
                <a href="{{ $products->previousPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ $products->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">Prev</a>
                <span class="min-w-7 rounded-md border border-[#63a2bb] bg-[#63a2bb] px-2.5 py-1.5 text-center text-xs font-extrabold text-white">{{ $products->currentPage() }}</span>
                <a href="{{ $products->nextPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ !$products->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">Next</a>
            </div>
        </div>
    </article>
</section>
@endsection
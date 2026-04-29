@extends('movr.layouts.admin')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $statusLabel = $product->stock > 0 ? 'Tersedia' : 'Stok Habis';
@endphp

<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-[30px] font-extrabold leading-none text-[#22344b]">Detail Produk</h1>
            <p class="mt-1 text-sm font-semibold text-[#9aa3b3]">Informasi lengkap produk master</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.produk.edit', $product->id) }}" class="inline-flex items-center gap-1.5 rounded-md border border-[#dbe1ea] bg-white px-3 py-2 text-xs font-bold text-[#5d6a80] hover:bg-[#f7f8fc]">
                <i class="fa-regular fa-pen-to-square"></i>
                Edit Produk
            </a>
            <a href="{{ route('admin.produk.index') }}" class="inline-flex items-center gap-1.5 rounded-md bg-[#63a2bb] px-3 py-2 text-xs font-bold text-white hover:brightness-95">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.1fr_1fr]">
        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            @php
                $coverImage = $product->image ? asset('storage/' . $product->image) : null;
            @endphp

            <div class="overflow-hidden rounded-lg border border-[#e7ebf2] bg-[#f8f9fd]">
                @if($coverImage)
                    <img src="{{ $coverImage }}" alt="{{ $product->name }}" class="h-[340px] w-full object-cover">
                @else
                    <div class="flex h-[340px] items-center justify-center text-[#8f98aa]">
                        <i class="fa-regular fa-image text-4xl"></i>
                    </div>
                @endif
            </div>

            @if($product->images && $product->images->count() > 0)
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach($product->images->take(5) as $image)
                        <div class="h-16 overflow-hidden rounded-md border border-[#e7ebf2]">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery {{ $loop->iteration }}" class="h-full w-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <h2 class="text-[24px] font-extrabold leading-none text-[#22344b]">{{ $product->name }}</h2>
            <p class="mt-1 text-sm font-semibold text-[#94a0b6]">ID Produk #PRD-{{ str_pad((string) $product->id, 3, '0', STR_PAD_LEFT) }}</p>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3">
                    <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Harga</p>
                    <p class="mt-1 text-[20px] font-extrabold text-[#22344b]">{{ $formatRupiah($product->price) }}</p>
                </div>

                <div class="rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3">
                    <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Status</p>
                    <p class="mt-1 text-[20px] font-extrabold {{ $product->stock > 0 ? 'text-[#08a65a]' : 'text-[#d12a34]' }}">{{ $statusLabel }}</p>
                </div>

                <div class="rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3">
                    <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Stok</p>
                    <p class="mt-1 text-[20px] font-extrabold text-[#22344b]">{{ number_format($product->stock) }}</p>
                </div>

                <div class="rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3">
                    <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Kategori</p>
                    <p class="mt-1 text-[20px] font-extrabold text-[#22344b]">{{ $product->category->name ?? '-' }}</p>
                </div>

                <div class="rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3 col-span-2">
                    <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Supplier</p>
                    <p class="mt-1 text-[20px] font-extrabold text-[#22344b]">{{ $product->supplier->store_name ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-4 rounded-lg border border-[#edf0f6] bg-[#fbfcff] p-3">
                <p class="text-[11px] font-bold uppercase text-[#a4aec0]">Deskripsi</p>
                <p class="mt-1 text-sm font-semibold leading-relaxed text-[#4f5d77]">{{ $product->description }}</p>
            </div>
        </article>
    </div>
</section>
@endsection

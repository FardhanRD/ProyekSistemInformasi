@extends('layouts.buyer')

@section('title', 'Hasil Pencarian — MOVR')

@section('content')
@php
    $products = $products ?? $produkList ?? collect();
    $searchQuery = $q ?? request('q');
@endphp

<div class="section-shell py-8 sm:py-10">
    <div class="mb-8 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Pencarian</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Hasil Pencarian</h1>
                <p class="mt-2 text-sm text-slate-500">
                    @if($searchQuery)
                        Menampilkan hasil untuk <span class="font-semibold text-slate-700">“{{ $searchQuery }}”</span>
                    @else
                        Masukkan kata kunci untuk melihat hasil pencarian produk.
                    @endif
                </p>
            </div>
            <div class="inline-flex items-center rounded-full bg-[#63A2BB]/10 px-4 py-2 text-sm font-semibold text-[#63A2BB]">
                {{ $products->total() }} produk ditemukan
            </div>
        </div>
    </div>

    <div class="card-surface p-5">
        @if($products->isEmpty())
            <div class="py-12 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#63A2BB]/10 text-[#63A2BB]">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-xl font-black text-slate-800">Tidak ada hasil</h3>
                <p class="mt-2 text-sm text-slate-500">Coba kata kunci lain atau jelajahi kategori produk.</p>
                <a href="{{ route('home') }}" class="btn-primary mt-6 inline-flex px-5 py-3 text-sm">Ke Beranda</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $p)
                    <x-product-card :produk="$p" />
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between gap-4">
                <p class="text-sm text-slate-500">Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk</p>
                <div>{{ $products->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
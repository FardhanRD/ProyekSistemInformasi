@extends('layouts.buyer')

@section('title', (($q ?? request('q')) ? __('ui.product_search_results') : __('ui.catalog')) . ' — MOVR')

@section('content')
@php
    $products = $products ?? $produkList ?? collect();
    $filterData = $filterData ?? [];
    $searchQuery = $q ?? request('q');
    $maxPrice = data_get($filterData, 'maxPrice', 1000000);
@endphp

<div class="section-shell py-8 sm:py-10">
    <div class="mb-8 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">{{ __('ui.catalog') }}</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">{{ $searchQuery ? __('ui.product_search_results') : __('ui.all_products') }}</h1>
                <p class="mt-2 text-sm text-slate-500">
                    @if($searchQuery)
                        {{ __('ui.search_page_showing') }} <span class="font-semibold text-slate-700">“{{ $searchQuery }}”</span>
                    @else
                        {{ __('ui.catalog_description') }}
                    @endif
                </p>
            </div>

            <div class="inline-flex items-center rounded-full bg-[#63A2BB]/10 px-4 py-2 text-sm font-semibold text-[#63A2BB]">
                {{ $products->total() }} {{ __('ui.search_page_found') }}
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <aside class="lg:col-span-3">
            <div class="card-surface sticky top-24 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">{{ __('ui.filter') }}</h2>
                    <a href="{{ request()->url() }}" class="text-sm font-semibold text-[#63A2BB] transition-all duration-200 hover:text-[#4A8BA3]">{{ __('ui.reset') }}</a>
                </div>

                <form action="{{ request()->url() }}" method="GET" class="mt-5 space-y-5">
                    @if($searchQuery)
                        <input type="hidden" name="q" value="{{ $searchQuery }}">
                    @endif

                    <div>
                        <div class="mb-2 text-sm font-semibold text-slate-700">{{ __('ui.category') }}</div>
                        <div class="max-h-56 space-y-2 overflow-auto pr-1">
                            @foreach(data_get($filterData, 'categories', []) as $category)
                                <label class="flex items-center gap-2 rounded-xl bg-[#F8FAFB] px-3 py-2 text-sm text-slate-600 ring-1 ring-slate-200/70 transition-all duration-200 hover:ring-[#63A2BB]/40">
                                    <input type="checkbox" name="kategori[]" value="{{ $category->kategori_id }}" {{ in_array($category->kategori_id, request('kategori', [])) ? 'checked' : '' }} class="accent-[#63A2BB]">
                                    <span class="min-w-0 flex-1 truncate">{{ $category->nama_kategori }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-semibold text-slate-700">{{ __('ui.price') }}</div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" min="0" placeholder="{{ __('ui.min') }}" class="rounded-2xl border border-slate-200 bg-[#F8FAFB] px-3 py-2 text-sm outline-none transition-all duration-200 focus:border-[#63A2BB] focus:bg-white focus:ring-4 focus:ring-[#63A2BB]/20">
                            <input type="number" name="max_price" value="{{ request('max_price', $maxPrice) }}" min="0" placeholder="{{ __('ui.max') }}" class="rounded-2xl border border-slate-200 bg-[#F8FAFB] px-3 py-2 text-sm outline-none transition-all duration-200 focus:border-[#63A2BB] focus:bg-white focus:ring-4 focus:ring-[#63A2BB]/20">
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-semibold text-slate-700">{{ __('ui.size') }}</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(data_get($filterData, 'sizes', []) as $size)
                                <label>
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, request('sizes', [])) ? 'checked' : '' }} class="peer sr-only">
                                    <span class="inline-flex cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition-all duration-200 peer-checked:border-[#63A2BB] peer-checked:bg-[#63A2BB] peer-checked:text-white hover:scale-105">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-semibold text-slate-700">{{ __('ui.minimum_rating') }}</div>
                        <div class="space-y-2">
                            @foreach([4, 3, 2, 1] as $star)
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-[#F8FAFB] px-3 py-2 text-sm text-slate-600 ring-1 ring-slate-200/70 transition-all duration-200 hover:ring-[#63A2BB]/40">
                                    <input type="radio" name="rating" value="{{ $star }}" {{ (string) request('rating') === (string) $star ? 'checked' : '' }} class="accent-[#63A2BB]">
                                    <div class="flex gap-0.5 text-amber-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3.5 w-3.5 {{ $i <= $star ? 'fill-amber-400' : 'fill-slate-200' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-slate-500">& ke atas</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-semibold text-slate-700">Urutkan</div>
                        <select name="sort" class="w-full rounded-2xl border border-slate-200 bg-[#F8FAFB] px-3 py-2 text-sm outline-none transition-all duration-200 focus:border-[#63A2BB] focus:bg-white focus:ring-4 focus:ring-[#63A2BB]/20">
                            <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlaris" {{ request('sort') === 'terlaris' ? 'selected' : '' }}>Terlaris</option>
                            <option value="harga_terendah" {{ request('sort') === 'harga_terendah' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="harga_tertinggi" {{ request('sort') === 'harga_tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="rating_tertinggi" {{ request('sort') === 'rating_tertinggi' ? 'selected' : '' }}>Rating Tertinggi</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center px-5 py-3 text-sm">Terapkan Filter</button>
                </form>
            </div>
        </aside>

        <section class="lg:col-span-9">
            <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
                @if($products->isEmpty())
                    <div class="p-12 text-center">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-5 text-xl font-black text-slate-800">Produk tidak ditemukan</h3>
                        <p class="mt-2 text-sm text-slate-500">Coba ubah filter atau kata kunci pencarian.</p>
                        <a href="{{ request()->url() }}" class="btn-outline mt-6 inline-flex px-5 py-3 text-sm">Reset Filter</a>
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
        </section>
    </div>
</div>
@endsection
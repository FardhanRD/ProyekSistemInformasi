{{--
  // ── FILE: resources/views/category/index.blade.php ──
  Category list w/ filter sidebar (Alpine) & grid ProductCard.
--}}

@extends('layouts.buyer')

@section('title', 'MOVR | Kategori')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
        <div>
            <div class="text-xs font-semibold text-cyan-300">KATEGORI</div>
            <h1 class="text-2xl md:text-3xl font-black">{{ $kategori->nama_kategori ?? '-' }}</h1>
            @if(!empty($kategori->slug))
                <p class="text-slate-300 text-sm mt-2">Slug: {{ $kategori->slug }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <div class="text-sm text-slate-300">{{ $produk->total() }} produk</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Sidebar Filters --}}
        <aside class="lg:col-span-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold">Filter</h2>
                    <button type="button" class="text-sm text-cyan-300 hover:underline" @click="resetFilters()">Reset</button>
                </div>

                <div x-data="categoryFilters()" class="mt-4 space-y-5">

                    {{-- Harga range --}}
                    <div>
                        <div class="text-sm font-semibold mb-2">Harga</div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none focus:border-cyan-400"
                                       placeholder="Min" x-model.number="minPrice" />
                                <input type="number" min="0" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none focus:border-cyan-400"
                                       placeholder="Max" x-model.number="maxPrice" />
                            </div>
                            <div class="text-xs text-slate-300">(Demo: akan diterapkan saat load ulang halaman)</div>
                        </div>
                    </div>

                    {{-- Rating checkboxes --}}
                    <div>
                        <div class="text-sm font-semibold mb-2">Rating</div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" class="accent-cyan-400" x-model="ratings" value="5" /> 5★
                        </label>
                        <label class="flex items-center gap-2 text-sm mt-2">
                            <input type="checkbox" class="accent-cyan-400" x-model="ratings" value="4" /> 4★+
                        </label>
                        <label class="flex items-center gap-2 text-sm mt-2">
                            <input type="checkbox" class="accent-cyan-400" x-model="ratings" value="3" /> 3★+
                        </label>
                    </div>

                    {{-- Ukuran --}}
                    <div>
                        <div class="text-sm font-semibold mb-2">Ukuran</div>
                        <div class="space-y-2 max-h-40 overflow-auto pr-2">
                            @php
                                $sizes = $ukuranList ?? [];
                            @endphp
                            @foreach($sizes as $s)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="accent-cyan-400" :value="'{{ $s }}'" x-model="sizes" /> {{ $s }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Warna swatch --}}
                    <div>
                        <div class="text-sm font-semibold mb-2">Warna</div>
                        <div class="grid grid-cols-5 gap-2">
                            @php $colors = $warnaList ?? []; @endphp
                            @foreach($colors as $w)
                                <button type="button"
                                        class="relative w-8 h-8 rounded-full border border-white/10"
                                        style="background-color: {{ $w->kode_hex }};"
                                        @click="toggleColor('{{ $w->warna_id }}', $event)"
                                        :class="colorsSelected.includes('{{ $w->warna_id }}') ? 'ring-2 ring-cyan-400' : ''">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <div class="text-sm font-semibold mb-2">Sort</div>
                        <select class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none focus:border-cyan-400"
                                x-model="sort">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlaris">Terlaris</option>
                            <option value="harga_asc">Harga ↑</option>
                            <option value="harga_desc">Harga ↓</option>
                            <option value="rating_desc">Rating</option>
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="button"
                                class="w-full rounded-2xl bg-cyan-500 px-4 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400"
                                @click="apply()">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Results Grid --}}
        <section class="lg:col-span-9">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($produk as $p)
                        @php
                            $kategoriNama = $p->kategori->nama_kategori ?? null;
                            $img = $p->gambarProduk()->where('urutan',0)->first();
                            $detail = \App\Models\DetailProduk::where('produk_id',$p->produk_id)->where('is_active',1)->first();
                        @endphp
                        @include('components.product-card', [
                            'image' => $img?->url_gambar,
                            'title' => $p->nama_produk,
                            'category' => $kategoriNama,
                            'rating' => $p->rata_rating,
                            'price' => $p->harga_dasar,
                            'productId' => $p->produk_id,
                            'detailId' => $detail?->detail_produk_id,
                            'addCartUrl' => route('cart.store'),
                            'detailUrl' => route('product.show', $p->slug),
                        ])
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $produk->withQueryString()->links() }}
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    function categoryFilters() {
        return {
            minPrice: null,
            maxPrice: null,
            ratings: [],
            sizes: [],
            colorsSelected: [],
            sort: 'terbaru',
            toggleColor(id) {
                id = String(id);
                if (this.colorsSelected.includes(id)) {
                    this.colorsSelected = this.colorsSelected.filter(x => x !== id);
                } else {
                    this.colorsSelected.push(id);
                }
            },
            resetFilters() {
                this.minPrice = null;
                this.maxPrice = null;
                this.ratings = [];
                this.sizes = [];
                this.colorsSelected = [];
                this.sort = 'terbaru';
                this.apply();
            },
            apply() {
                // Untuk versi awal (tanpa AJAX), redirect ke URL dengan querystring.
                const params = new URLSearchParams(window.location.search);
                if (this.minPrice !== null && this.minPrice !== undefined) params.set('min_price', this.minPrice);
                if (this.maxPrice !== null && this.maxPrice !== undefined) params.set('max_price', this.maxPrice);
                params.set('sort', this.sort);
                window.location.search = params.toString();
            }
        }
    }
</script>
@endsection


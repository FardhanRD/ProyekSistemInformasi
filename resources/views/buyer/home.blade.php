@extends('layouts.buyer')

@section('title', 'Home - MOVR')

@section('content')
<div class="section-shell py-8 sm:py-10 space-y-10">
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 text-white shadow-xl shadow-slate-200/40" x-data="heroSlider()" x-init="init()" data-banners='@json($banners ?? [])'>
        <div class="absolute inset-0 bg-gradient-to-r from-[#63A2BB]/95 via-[#4A8BA3]/90 to-slate-950"></div>
        <div class="relative">
            <template x-for="(banner, index) in banners" :key="index">
                <div x-show="current === index" x-transition class="grid min-h-[360px] items-center gap-10 px-6 py-10 md:grid-cols-2 md:px-12 md:py-16">
                    <div>
                        <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-white/70">New Collection</p>
                        <h1 class="max-w-xl text-4xl font-black leading-tight md:text-6xl" x-text="banner.judul || 'Selamat datang di MOVR'"></h1>
                        <p class="mt-4 max-w-xl text-base leading-7 text-white/80 md:text-lg" x-text="banner.sub_judul || 'Temukan produk favoritmu di sini'"></p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a :href="banner.url_link || '/produk'" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-[#63A2BB] transition-all duration-200 ease-in-out hover:scale-[1.02] hover:shadow-lg">
                                Belanja Sekarang
                            </a>
                            <a href="{{ route('product.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/30 px-6 py-3 text-sm font-bold text-white transition-all duration-200 ease-in-out hover:bg-white/10 hover:scale-[1.02]">
                                Lihat Katalog
                            </a>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <div class="rounded-[2rem] border border-white/15 bg-white/10 p-4 backdrop-blur-md shadow-2xl">
                            <img :src="banner.url_gambar || '{{ asset('images/default-banner.svg') }}'" alt="Banner" class="h-[320px] w-full rounded-[1.5rem] object-cover">
                        </div>
                    </div>
                </div>
            </template>

            @if(empty($banners) || (is_countable($banners) && count($banners) === 0))
                <div class="grid min-h-[360px] items-center gap-10 px-6 py-10 md:grid-cols-2 md:px-12 md:py-16">
                    <div>
                        <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-white/70">New Collection 2026</p>
                        <h1 class="max-w-xl text-4xl font-black leading-tight md:text-6xl">Move With <span class="text-white/80">Style & Comfort</span></h1>
                        <p class="mt-4 max-w-xl text-base leading-7 text-white/80 md:text-lg">Koleksi terbaru untuk gaya hidup aktif kamu.</p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('product.index') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-[#63A2BB] transition-all duration-200 ease-in-out hover:scale-[1.02] hover:shadow-lg">Explore Collection</a>
                            <a href="{{ route('category.show', 'all') }}" class="inline-flex items-center justify-center rounded-full border border-white/30 px-6 py-3 text-sm font-bold text-white transition-all duration-200 ease-in-out hover:bg-white/10 hover:scale-[1.02]">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="rounded-[2rem] border border-white/15 bg-white/10 p-4 backdrop-blur-md shadow-2xl">
                            <img src="{{ asset('images/default-banner.svg') }}" alt="Banner default" class="h-[320px] w-full rounded-[1.5rem] object-cover">
                        </div>
                    </div>
                </div>
            @endif

            @if((is_countable($banners) ? count($banners) : 0) > 1)
                <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur-md transition-all duration-200 hover:scale-105 hover:bg-white/25">‹</button>
                <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur-md transition-all duration-200 hover:scale-105 hover:bg-white/25">›</button>
            @endif
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        @foreach([
            ['label' => 'Quick Shipping', 'value' => 'Pengiriman cepat dan aman', 'icon' => 'truck'],
            ['label' => 'Secure Payment', 'value' => 'Pembayaran nyaman dan terverifikasi', 'icon' => 'shield'],
            ['label' => 'Premium Picks', 'value' => 'Koleksi pilihan untuk gaya modern', 'icon' => 'sparkles'],
        ] as $feature)
            <div class="card-surface card-hover p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-[#63A2BB]">
                        @if($feature['icon'] === 'truck')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h11v8H3zM14 9h4l3 3v3h-7zM7 18a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" /></svg>
                        @elseif($feature['icon'] === 'shield')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4z" /></svg>
                        @else
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3l2.7 5.5L21 10l-4 3.8 1 5.7-5-2.6-5 2.6 1-5.7L5 10l5.3-1.5L13 3z" /></svg>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ $feature['label'] }}</div>
                        <div class="text-sm text-slate-500">{{ $feature['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="space-y-5">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Produk Unggulan</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">New Arrivals</h2>
            </div>
            <a href="{{ route('product.index') }}?sort=terbaru" class="text-sm font-semibold text-[#63A2BB] transition-all duration-200 hover:text-[#4A8BA3]">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach($newArrivals as $p)
                <x-product-card :produk="$p" :badge="'NEW'" :showWishlistBtn="true" />
            @endforeach
        </div>
    </section>

    <section class="space-y-5 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Terpopuler</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">Best Sellers</h2>
            </div>
            <a href="{{ route('product.index') }}?sort=terlaris" class="text-sm font-semibold text-[#63A2BB] transition-all duration-200 hover:text-[#4A8BA3]">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach($bestSellers as $p)
                <x-product-card :produk="$p" :showWishlistBtn="true" />
            @endforeach
        </div>
    </section>

    @if($flashProducts->isNotEmpty())
        <section class="space-y-5 rounded-[2rem] border border-[#63A2BB]/15 bg-[#63A2BB]/5 p-6">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Limited Time</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900">Flash Sale</h2>
                </div>
                <div x-data="{ end: '{{ optional($flashProducts->first()->promo->selesai ?? now())->toDateTimeString() }}', diff:0, tick(){ let d = new Date(this.end) - new Date(); if(d < 0) d = 0; this.diff = d; setTimeout(() => this.tick(), 1000); }, h(){ return Math.floor(this.diff/3600000)%24 }, m(){ return Math.floor(this.diff/60000)%60 }, s(){ return Math.floor(this.diff/1000)%60 } }" x-init="tick()" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                    Berakhir dalam <span x-text="h()"></span>j <span x-text="m()"></span>m <span x-text="s()"></span>s
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach($flashProducts as $fp)
                    @php $p = $fp->produk; $promo = $fp->promo; @endphp
                    <x-product-card :produk="$p" :promo="$promo" :badge="'SALE'" :showWishlistBtn="true" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="space-y-5">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Cepat Menjelajah</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">Kategori Cepat</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach($quickCategories as $c)
                <a href="{{ route('category.show', $c->slug) }}" class="group overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-xl hover:shadow-[#63A2BB]/15">
                    <div class="aspect-[4/3] overflow-hidden bg-[#F1F5F8]">
                        <img src="{{ $c->banner_url ?? asset('images/default-category.svg') }}" class="h-full w-full object-cover transition-all duration-300 group-hover:scale-105" alt="{{ $c->nama_kategori }}">
                    </div>
                    <div class="p-5">
                        <div class="text-sm font-bold text-slate-900">{{ $c->nama_kategori }}</div>
                        <div class="mt-1 text-sm text-slate-500">Buka kategori</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    function heroSlider() {
        return {
            current: 0,
            banners: [],
            init() {
                const rawBanners = this.$el.dataset.banners || '[]';
                this.banners = JSON.parse(rawBanners);

                if (this.banners.length > 1) {
                    setInterval(() => this.next(), 5000);
                }
            },
            next() {
                if (!this.banners.length) return;
                this.current = (this.current + 1) % this.banners.length;
            },
            prev() {
                if (!this.banners.length) return;
                this.current = (this.current - 1 + this.banners.length) % this.banners.length;
            },
        }
    }
</script>
@endsection

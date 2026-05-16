@extends('layouts.buyer')

@section('title','Home - MOVR')

@section('content')
    <script>
        window.BANNER_DATA = @json($banners);
    </script>

    {{-- Banner Slider --}}
    <div x-data="{ idx:0, banners: window.BANNER_DATA, next(){ this.idx = (this.idx+1) % this.banners.length }, prev(){ this.idx = (this.idx-1+this.banners.length) % this.banners.length }, init(){setInterval(()=>this.next(),5000)} }" x-init="init()">
        <div class="relative overflow-hidden rounded-lg">
            <template x-for="(b,i) in banners" :key="i">
                <div x-show="i===idx" x-transition class="w-full">
                    <div class="w-full h-64 bg-gray-300 flex items-center justify-center" :style="{ backgroundImage: 'url(' + b.url_gambar + ')', backgroundSize: 'cover', backgroundPosition: 'center', backgroundColor: '#e5e7eb' }">
                        <div class="bg-black/40 text-white p-6 rounded-lg max-w-xl">
                            <h2 class="text-2xl font-bold" x-text="b.judul"></h2>
                            <p class="mt-2" x-text="b.sub_judul"></p>
                            <a :href="b.url_link || '/produk'" class="inline-block mt-3 bg-white text-black px-4 py-2 rounded">Belanja Sekarang</a>
                        </div>
                    </div>
                </div>
            </template>
            <button @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-2">‹</button>
            <button @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-2">›</button>
        </div>
    </div>

    {{-- New Arrivals --}}
    <section class="mt-8">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">New Arrivals</h3>
            <a href="/category/all?sort=terbaru" class="text-sm text-blue-600">Lihat Semua</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <div class="flex gap-4">
                @foreach($newArrivals as $p)
                    <div class="w-48 flex-shrink-0">
                        <x-product-card :produk="$p" :badge="'NEW'" :showWishlistBtn="true" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Best Sellers --}}
    <section class="mt-8">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Best Sellers</h3>
            <a href="/category/all?sort=terlaris" class="text-sm text-blue-600">Lihat Semua</a>
        </div>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($bestSellers as $p)
                <x-product-card :produk="$p" :showWishlistBtn="true" />
            @endforeach
        </div>
    </section>

    {{-- Flash Sale / On Sale --}}
    @if($flashProducts->isNotEmpty())
    <script>
        window.FLASH_END_TIME = "{{ optional($flashProducts->first()->promo->selesai ?? now())->toDateTimeString() }}";
    </script>
    <section class="mt-8">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">Flash Sale</h3>
            <div x-data="{ end: window.FLASH_END_TIME, diff:0, tick(){ let d = new Date(this.end) - new Date(); if(d<0) d=0; this.diff=d; setTimeout(()=>this.tick(),1000) }, h(){ return Math.floor(this.diff/3600000)%24 }, m(){ return Math.floor(this.diff/60000)%60 }, s(){ return Math.floor(this.diff/1000)%60 } }" x-init="tick()">
                <span class="text-sm text-red-600">Berakhir dalam <strong x-text="h()"></strong>j <strong x-text="m()"></strong>m <strong x-text="s()"></strong>s</span>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($flashProducts as $fp)
                @php $p = $fp->produk; $promo = $fp->promo; @endphp
                <x-product-card :produk="$p" :promo="$promo" :badge="'SALE'" :showWishlistBtn="true" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Quick Categories --}}
    <section class="mt-8">
        <h3 class="text-xl font-semibold">Kategori Cepat</h3>
        <div class="mt-4 grid grid-cols-3 gap-4">
            @foreach($quickCategories as $c)
                <a href="/category/{{ $c->slug }}" class="block bg-white rounded overflow-hidden shadow">
                    <img src="{{ $c->banner_url ?? asset('images/default-category.svg') }}" class="w-full h-44 object-cover" alt="">
                    <div class="p-4">
                        <div class="font-semibold">{{ $c->nama_kategori }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

@endsection

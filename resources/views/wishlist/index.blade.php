{{--
  // ── FILE: resources/views/wishlist/index.blade.php ──
  Wishlist grid + move to cart + remove.
--}}

@extends('layouts.buyer')

@section('title', __('ui.wishlist') . ' | MOVR')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold text-cyan-300">{{ __('ui.wishlist') }}</div>
        <h1 class="text-2xl md:text-3xl font-black">{{ __('ui.wishlist_heading') }}</h1>
    </div>

    @if(empty($items) || $items->isEmpty())
        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
            <div class="text-6xl">♡</div>
            <div class="mt-3 text-lg font-bold">{{ __('ui.wishlist_empty_title') }}</div>
            <div class="text-slate-300 text-sm mt-2">{{ __('ui.wishlist_empty_desc') }}</div>
            <a href="{{ url('/') }}" class="mt-6 inline-flex rounded-full bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">{{ __('ui.browse_products') }}</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($items as $w)
                @php
                    $produk = $w->produk ?? null;
                    $img = $produk?->gambarProduk()?->where('urutan',0)->first();
                    $kategoriNama = $produk?->kategori?->nama_kategori ?? null;
                    $detail = \App\Models\DetailProduk::where('produk_id',$produk->produk_id)->where('is_active',1)->first();
                @endphp

                <div class="group rounded-3xl border border-white/10 bg-white/5 hover:bg-white/10 transition overflow-hidden">
                    <div class="relative">
                        <a href="{{ route('product.show', $produk->slug ?? '') }}" class="block">
                            <img src="{{ $img?->url_gambar ?? 'https://via.placeholder.com/500x400?text=MOVR' }}" alt="{{ $produk?->nama_produk ?? '' }}" class="h-56 w-full object-cover" />
                        </a>
                        <form method="post" action="{{ route('wishlist.toggle') }}" class="absolute top-3 right-3">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $produk->produk_id }}" />
                            <button type="submit" class="rounded-full border border-white/15 bg-black/30 backdrop-blur px-3 py-2 text-sm hover:bg-black/40" aria-label="{{ __('ui.remove_item') }}">✕</button>
                        </form>
                        <div class="absolute bottom-3 left-3 inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/30 backdrop-blur px-3 py-1 text-xs text-slate-200">
                            <span class="text-cyan-300">★</span>
                            <span>{{ number_format((float)($produk->rata_rating ?? 0),1) }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="text-xs font-semibold text-slate-200">{{ $kategoriNama }}</div>
                        <a href="{{ route('product.show', $produk->slug ?? '') }}" class="block mt-2 text-sm font-semibold text-white hover:text-cyan-300 line-clamp-2">
                            {{ $produk->nama_produk }}
                        </a>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="font-black">Rp {{ number_format((int)($produk->harga_dasar ?? 0),0,',','.') }}</div>
                            <div class="flex gap-2">
                                <a href="{{ route('product.show', $produk->slug ?? '') }}" class="rounded-full border border-white/10 px-3 py-2 text-xs font-bold hover:bg-white/5">
                                    {{ __('ui.pick_variant') }}
                                </a>
                                @auth
                                    <form method="post" action="{{ route('cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="detail_produk_id" value="{{ $detail->detail_produk_id ?? '' }}" />
                                        <input type="hidden" name="jumlah" value="1" />
                                        <button type="submit" class="rounded-full bg-cyan-500 px-3 py-2 text-xs font-bold text-slate-950 hover:bg-cyan-400" @disabled(empty($detail))>
                                            {{ __('ui.move_to_cart') }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-full bg-cyan-500 px-3 py-2 text-xs font-bold text-slate-950 hover:bg-cyan-400">
                                        {{ __('ui.login') }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection


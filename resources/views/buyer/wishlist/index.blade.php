@extends('layouts.buyer')
@section('title', __('ui.wishlist') . ' — MOVR')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-black text-gray-900">{{ __('ui.wishlist') }}</h1>
      <p class="text-gray-400 text-sm mt-1">
        {{ $wishlists->count() }} {{ __('ui.wishlist_saved_count') }}
      </p>
    </div>
  </div>

  @if($wishlists->isEmpty())
  <div class="flex flex-col items-center justify-center 
              py-24 text-center">
    <div class="w-24 h-24 bg-red-50 rounded-full 
                flex items-center justify-center mb-6">
      <svg class="w-12 h-12 text-red-300" 
           fill="none" stroke="currentColor" 
           viewBox="0 0 24 24">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="1.5"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 
                 20.364l7.682-7.682a4.5 4.5 0 00
                 -6.364-6.364L12 7.636l-1.318-1.318a4.5 
                 4.5 0 00-6.364 0z"/>
      </svg>
    </div>
    <h2 class="text-xl font-bold text-gray-700 mb-2">
      {{ __('ui.wishlist_empty_title') }}
    </h2>
    <p class="text-gray-400 mb-8">
      {{ __('ui.wishlist_empty_hint') }}
    </p>
    <a href="/" class="btn-primary">
      {{ __('ui.wishlist_explore_products') }}
    </a>
  </div>
  @else

  <div id="wishlist-grid"
       class="grid grid-cols-2 md:grid-cols-3 
              lg:grid-cols-4 gap-4 md:gap-6">
    @foreach($wishlists as $item)
    <div id="wl-card-{{ $item->produk_id }}"
         class="group bg-white rounded-3xl overflow-hidden 
                shadow-sm hover:shadow-xl transition-all 
                duration-300 hover:-translate-y-1">
      
      <div class="relative overflow-hidden aspect-[3/4] 
                  bg-gray-50">
        <a href="{{ route('product.show', 
                    $item->produk->slug) }}">
          @if($item->produk->gambarUtama)
          <img src="{{ $item->produk->gambarUtama->url_safe }}"
               alt="{{ $item->produk->nama_produk }}"
               class="w-full h-full object-cover 
                      group-hover:scale-105 
                      transition-transform duration-500">
          @endif
        </a>

        {{-- Wishlist Toggle (merah = aktif) --}}
        <div class="absolute top-3 right-3"
             x-data="{ 
               loading: false,
               async remove() {
                 this.loading = true;
                 const res = await fetch(
                   '{{ route('wishlist.toggle') }}', {
                   method: 'POST',
                   headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },
                   body: JSON.stringify({ 
                     produk_id: {{ $item->produk_id }} 
                   })
                 });
                 const data = await res.json();
                 if (data.success) {
                   const card = document.getElementById(
                     'wl-card-{{ $item->produk_id }}');
                   card.style.transition = 'all 0.3s';
                   card.style.opacity = '0';
                   card.style.transform = 'scale(0.85)';
                   setTimeout(() => card.remove(), 300);
                   const badge = document.getElementById(
                     'wishlist-count');
                   if (badge) badge.textContent = data.count;
                   showToast(@json(__('ui.wishlist_removed')));
                 }
                 this.loading = false;
               }
             }">
          <button @click.prevent="remove()"
                  :disabled="loading"
                  class="w-9 h-9 rounded-full bg-white/90 
                         shadow-md flex items-center 
                         justify-center hover:scale-110 
                         transition">
            <svg class="w-4 h-4 text-red-500 fill-red-500" 
                 viewBox="0 0 24 24">
              <path d="M12 21.593c-5.63-5.539-11
                       -10.297-11-14.402 0-3.791 
                       3.068-5.191 5.281-5.191 1.312 
                       0 4.151.501 5.719 4.457 
                       1.59-3.968 4.464-4.447 5.726
                       -4.447 2.54 0 5.274 1.621 
                       5.274 5.181 0 4.069-5.136 
                       8.625-11 14.402z"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="p-4">
        <a href="{{ route('product.show', 
                    $item->produk->slug) }}"
           class="font-semibold text-sm text-gray-800 
                  hover:text-[#63A2BB] line-clamp-2 
                  transition">
          {{ $item->produk->nama_produk }}
        </a>
        <div class="flex items-center gap-1 mt-1.5">
          <svg class="w-3 h-3 text-amber-400 fill-amber-400" 
               viewBox="0 0 20 20">
            <path d="M9.049 2.927..."/>
          </svg>
          <span class="text-xs text-gray-400">
            {{ number_format($item->produk->rata_rating,1) }}
          </span>
        </div>
        <p class="text-[#63A2BB] font-black mt-2 text-sm">
          Rp {{ number_format(
            $item->produk->harga_dasar,0,',','.') }}
        </p>
        <a href="{{ route('product.show', 
                    $item->produk->slug) }}"
           class="mt-3 block w-full text-center 
                  bg-[#63A2BB]/10 text-[#63A2BB] 
                  py-2.5 rounded-xl text-xs font-bold 
                  hover:bg-[#63A2BB] hover:text-white 
                  transition-all">
          {{ __('ui.view_detail') }} →
        </a>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endsection

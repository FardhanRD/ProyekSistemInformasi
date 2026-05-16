@props(['produk', 'showWishlistBtn' => true, 'badge' => null, 'promo' => null])

@php
    use App\Models\Wishlist;
    
    $isLoggedIn = auth()->check();
    $wishlistOwnerColumn = Wishlist::ownerColumn();
    $wishlistOwnerId = $isLoggedIn ? Wishlist::resolveOwnerId(auth()->user()) : null;
    $isInWishlist = $isLoggedIn && $wishlistOwnerId
        ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->where('produk_id', $produk->produk_id)->exists()
        : false;
    
    $avgRating = $produk->rata_rating ?? (isset($produk->ratings) ? ($produk->ratings->avg('bintang') ?? 0) : 0);
    $reviewCount = $produk->jumlah_ulasan ?? (isset($produk->ratings) ? $produk->ratings->count() : 0);

    
    $promoDiscount = 0;
    if (!empty($promo)) {
        $promoDiscount = (float) ($promo->diskon ?? $promo->nominal_diskon ?? 0);
        if ($promoDiscount <= 0 && !empty($promo->persen_diskon)) {
            $promoDiscount = ((float) $produk->harga_dasar) * ((float) $promo->persen_diskon) / 100;
        }
    }
@endphp

<div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col h-full" x-data="{ isWishlisted: {{ $isInWishlist ? 'true' : 'false' }}, async toggleWishlist() { if (!{{ $isLoggedIn ? 'true' : 'false' }}) { alert('Silakan login terlebih dahulu'); window.location.href = '{{ route('login') }}'; return; } const fd = new FormData(); fd.append('produk_id', {{ $produk->produk_id }}); try { const endpoint = this.isWishlisted ? '/wishlist/remove?produk_id={{ $produk->produk_id }}' : '/wishlist/add'; const res = await fetch(endpoint, { method: this.isWishlisted ? 'DELETE' : 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'), 'Accept': 'application/json' }, body: this.isWishlisted ? null : fd }); const data = await res.json(); if (data.success) { const wasWishlisted = this.isWishlisted; this.isWishlisted = !this.isWishlisted; if (wasWishlisted && !this.isWishlisted) { window.dispatchEvent(new CustomEvent('wishlist-removed', { detail: { produk_id: {{ $produk->produk_id }} } })); } window.dispatchEvent(new Event('wishlist-updated')); } } catch (e) { console.error('Wishlist error:', e); } } }">
    {{-- Image Container --}}
    <a href="/product/{{ $produk->slug }}" class="relative overflow-hidden bg-gray-100 flex-shrink-0" style="height: 200px;">
        <img src="{{ optional($produk->images->first())->url_lengkap ?? (optional($produk->images->first())->url_gambar ? asset('storage/' . $produk->images->first()->url_gambar) : asset('images/default-product.svg')) }}"
             alt="{{ $produk->nama_produk }}" 
             class="w-full h-full object-cover hover:scale-110 transition"
             onerror="this.src='{{ asset('images/default-product.svg') }}'; this.classList.add('opacity-50');">
        
            {{-- Wishlist Button --}}
            @if($showWishlistBtn ?? true)
            <button @click.stop="toggleWishlist()"
                    class="absolute top-3 right-3 p-2 rounded-full backdrop-blur-sm transition z-10"
                    :class="isWishlisted ? 'bg-red-500 text-white' : 'bg-white/80 text-gray-600'">
                <span x-text="isWishlisted ? '❤' : '🤍'" class="text-lg"></span>
            </button>
            @endif
        
        {{-- Badge --}}
        @if(!empty($badge ?? null))
        <div class="absolute top-3 left-3 bg-red-600 text-white px-2 py-1 text-xs font-semibold rounded">
            {{ $badge }}
        </div>
        @endif
    </a>
    
    {{-- Content Container --}}
    <div class="p-3 flex-grow flex flex-col justify-between">
        {{-- Product Name & Rating --}}
        <a href="/product/{{ $produk->slug }}" class="text-sm font-semibold text-gray-800 hover:text-blue-600 line-clamp-2">
            {{ $produk->nama_produk }}
        </a>
        
        {{-- Rating & Review Count --}}
        <div class="flex items-center gap-2 mt-1 text-xs text-gray-600">
            <span class="flex items-center gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($avgRating))
                        <span class="text-yellow-400">★</span>
                    @elseif($i - $avgRating < 1)
                        <span class="text-yellow-400">⭐</span>
                    @else
                        <span class="text-gray-300">☆</span>
                    @endif
                @endfor
            </span>
            <span>({{ round($avgRating, 1) }})</span>
            <span>·</span>
            <span>{{ $reviewCount }}</span>
        </div>
        
        {{-- Pricing --}}
        <div class="mt-3 flex flex-col gap-1">
            @if($promoDiscount > 0)
                <div class="text-xs text-gray-500 line-through">
                    Rp {{ number_format($produk->harga_dasar) }}
                </div>
                <div class="text-sm font-bold text-red-600">
                    Rp {{ number_format($produk->harga_dasar - $promoDiscount) }}
                </div>
            @else
                <div class="text-sm font-bold text-gray-800">
                    Rp {{ number_format($produk->harga_dasar) }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Inline Alpine handler used; no global script required -->

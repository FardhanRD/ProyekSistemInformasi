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
    if (! empty($promo)) {
        $promoDiscount = (float) ($promo->diskon ?? $promo->nominal_diskon ?? 0);
        if ($promoDiscount <= 0 && ! empty($promo->persen_diskon)) {
            $promoDiscount = ((float) $produk->harga_dasar) * ((float) $promo->persen_diskon) / 100;
        }
    }

    $imageSource = optional($produk->images->first())->url_lengkap
        ?? (optional($produk->images->first())->url_gambar ? asset('storage/' . $produk->images->first()->url_gambar) : asset('images/default-product.svg'));
    $detailId = optional($produk->details->first())->detail_produk_id;
    $finalPrice = $promoDiscount > 0 ? max(0, (float) $produk->harga_dasar - $promoDiscount) : (float) $produk->harga_dasar;
@endphp

<div class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-200 ease-in-out hover:scale-[1.02] hover:shadow-xl hover:shadow-[#63A2BB]/15" x-data="{ isWishlisted: {{ $isInWishlist ? 'true' : 'false' }}, loading: false, async toggleWishlist() { if (!{{ $isLoggedIn ? 'true' : 'false' }}) { window.location.href = '{{ route('login') }}'; return; } this.loading = true; try { const res = await fetch('{{ route('wishlist.toggle') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify({ produk_id: {{ $produk->produk_id }} }) }); const data = await res.json(); if (data.success) { const wasWishlisted = this.isWishlisted; this.isWishlisted = !this.isWishlisted; window.dispatchEvent(new Event('wishlist-updated')); if (wasWishlisted && !this.isWishlisted) { window.dispatchEvent(new CustomEvent('wishlist-removed', { detail: { produk_id: {{ $produk->produk_id }} } })); } if (typeof showToast === 'function') { showToast(this.isWishlisted ? '{{ __('ui.product_added_wishlist') }}' : '{{ __('ui.product_removed_wishlist') }}', this.isWishlisted ? 'success' : 'info'); } } else if (data.message && typeof showToast === 'function') { showToast(data.message, 'error'); } } catch (error) { console.error(error); if (typeof showToast === 'function') { showToast('{{ __('ui.wishlist_update_failed') }}', 'error'); } } finally { this.loading = false; } }, async addToCart() { if (!{{ $isLoggedIn ? 'true' : 'false' }}) { window.location.href = '{{ route('login') }}'; return; } const detailId = {{ $detailId ? (int) $detailId : 'null' }}; if (!detailId) { if (typeof showToast === 'function') { showToast('{{ __('ui.product_variant_unavailable') }}', 'warning'); } return; } try { const fd = new FormData(); fd.append('detail_produk_id', detailId); fd.append('jumlah', 1); const res = await fetch('{{ route('cart.add') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'), 'Accept': 'application/json' }, body: fd }); const data = await res.json(); if (data.success) { window.dispatchEvent(new Event('cart-updated')); if (typeof showToast === 'function') { showToast(data.message || '{{ __('ui.product_added_cart') }}', 'success'); } } else if (typeof showToast === 'function') { showToast(data.message || '{{ __('ui.product_add_cart_failed') }}', 'error'); } } catch (error) { console.error(error); if (typeof showToast === 'function') { showToast('{{ __('ui.product_add_cart_failed') }}', 'error'); } } } }">
    <div class="relative aspect-[3/4] overflow-hidden bg-[#F1F5F8]">
        <a href="{{ route('product.show', $produk->slug) }}" class="block h-full w-full">
            <img src="{{ $imageSource }}" alt="{{ $produk->nama_produk }}" class="h-full w-full object-cover transition-all duration-300 ease-in-out group-hover:scale-105" onerror="this.src='{{ asset('images/default-product.svg') }}';">
        </a>

        @if(!empty($badge))
            <span class="absolute left-3 top-3 rounded-full bg-[#63A2BB] px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white shadow-md">
                {{ $badge }}
            </span>
        @endif

        @if($showWishlistBtn)
            <button type="button" @click.stop="toggleWishlist()" :disabled="loading" class="absolute right-3 top-3 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-slate-700 shadow-md transition-all duration-200 ease-in-out hover:scale-105 hover:text-[#EF4444]">
                <svg x-show="!isWishlisted" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 10-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <svg x-cloak x-show="isWishlisted" class="h-5 w-5 text-[#EF4444] fill-[#EF4444]" viewBox="0 0 24 24">
                    <path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z" />
                </svg>
            </button>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <a href="{{ route('product.show', $produk->slug) }}" class="line-clamp-2 text-sm font-bold text-slate-900 transition-all duration-200 hover:text-[#63A2BB]">
            {{ $produk->nama_produk }}
        </a>

        <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
            <div class="flex items-center gap-0.5 text-amber-400">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="h-3.5 w-3.5 {{ $i <= round($avgRating) ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
            <span>({{ round($avgRating, 1) }})</span>
            <span>•</span>
            <span>{{ $reviewCount }} {{ __('ui.review_count') }}</span>
        </div>

        <div class="mt-3 flex items-end justify-between gap-3">
            <div>
                @if($promoDiscount > 0)
                    <div class="text-xs text-slate-400 line-through">Rp {{ number_format((float) $produk->harga_dasar, 0, ',', '.') }}</div>
                @endif
                <div class="text-lg font-black text-[#63A2BB]">Rp {{ number_format((float) $finalPrice, 0, ',', '.') }}</div>
            </div>
            @if(($produk->total_terjual ?? 0) > 0)
                <div class="text-xs font-medium text-slate-400">{{ number_format((int) $produk->total_terjual) }} {{ __('ui.products_sold') }}</div>
            @endif
        </div>

        <div class="mt-4 grid gap-2">
            <a href="{{ route('product.show', $produk->slug) }}" class="btn-outline w-full px-4 py-3 text-sm">
                {{ __('ui.view_detail') }}
            </a>
        </div>
    </div>
</div>

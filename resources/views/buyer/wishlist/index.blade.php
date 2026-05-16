@extends('layouts.buyer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Wishlist</h1>

        @if($items->isEmpty())
            <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
                <img src="{{ asset('images/wishlist-empty.svg') }}" alt="Wishlist kosong" class="mx-auto mb-6 h-48 w-48 object-contain">
                <p class="text-slate-300 mb-4">Wishlist Anda kosong.</p>
                <a href="{{ route('home') }}" class="inline-flex rounded-full bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">Jelajahi Produk</a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="wishlist-grid">
                @foreach($items as $item)
                    <div data-wishlist-item="{{ $item->produk->produk_id }}" class="transform transition duration-300">
                        <x-product-card :produk="$item->produk" :showWishlistBtn="true" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    window.addEventListener('wishlist-removed', (event) => {
        const id = event.detail?.produk_id;
        if (!id) return;

        const card = document.querySelector('[data-wishlist-item="' + id + '"]');
        if (!card) return;

        card.style.opacity = '0';
        card.style.transform = 'scale(0.96)';
        card.style.pointerEvents = 'none';
        setTimeout(() => card.remove(), 250);
    });
</script>
@endsection

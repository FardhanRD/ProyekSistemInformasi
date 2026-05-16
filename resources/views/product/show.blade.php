@extends('layouts.app')

@section('title',$product->nama_produk)

@section('content')
@php
    $initialDetail = $details->first();
    $averageRating = $product->average_rating ?? 0;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="lg:col-span-1" x-data="{ activeImage: 0, images: {{ json_encode($images->map->url_gambar) }} }">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4">
            <template x-if="images.length > 0">
                <img :src="images[activeImage]" class="w-full h-[400px] md:h-[500px] object-cover" alt="{{ $product->nama_produk }}">
            </template>
            <template x-if="images.length === 0">
                <img src="https://via.placeholder.com/600x400?text=No+Image" class="w-full h-[400px] md:h-[500px] object-cover" alt="No Image">
            </template>
        </div>
        
        <div class="flex gap-3 overflow-x-auto pb-2">
            <template x-for="(img, index) in images" :key="index">
                <button type="button" @click="activeImage = index" class="shrink-0 rounded-xl overflow-hidden border-2" :class="activeImage === index ? 'border-brand' : 'border-transparent opacity-70 hover:opacity-100'">
                    <img :src="img" class="w-20 h-20 object-cover" />
                </button>
            </template>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm h-full">
            <div class="text-brand font-bold text-sm tracking-wider uppercase mb-2">{{ $product->kategori->nama_kategori ?? 'Kategori' }}</div>
            <h1 class="text-3xl font-black text-slate-800 mb-2">{{ $product->nama_produk }}</h1>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="inline-flex items-center gap-1 bg-amber-100 px-3 py-1 rounded-full text-amber-700 font-bold text-sm">
                    <span>★</span>
                    <span>{{ number_format($averageRating, 1) }}</span>
                </div>
                <span class="text-slate-500 text-sm">({{ $product->ratings->count() }} ulasan)</span>
            </div>
            
            <div class="mb-4">
                <div class="text-slate-500 text-sm font-medium">Harga</div>
                <div class="text-3xl font-black text-slate-800" id="currentPrice">Rp {{ number_format($initialDetail->harga ?? $product->harga_dasar,0,',','.') }}</div>
            </div>

            <p class="text-slate-600 mb-6 leading-relaxed">{{ $product->deskripsi }}</p>

            <div class="bg-brand/10 border border-brand/20 rounded-xl p-3 mb-6 flex items-center gap-3 text-sm text-brand-dark">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Stok tersedia: <strong id="currentStock" class="font-black">{{ $initialDetail->stok ?? 0 }}</strong>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-3">Pilih Varian (Warna / Ukuran)</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($details as $detail)
                        <button
                            type="button"
                            class="variant-btn rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $loop->first ? 'bg-brand border-brand text-white' : 'bg-white border-slate-200 text-slate-700 hover:border-brand/50' }}"
                            data-detail-id="{{ $detail->detail_produk_id }}"
                            data-price="{{ $detail->harga }}"
                            data-stock="{{ $detail->stok }}"
                        >
                            {{ $detail->warna->nama_warna ?? ($detail->warna_id ? 'Warna #'.$detail->warna_id : 'Default') }} - {{ $detail->ukuran ?? '-' }}
                        </button>
                    @endforeach
                </div>
            </div>

            <form method="post" action="{{ route('cart.store') }}" class="flex flex-wrap items-end gap-3 mb-6">
                @csrf
                <input type="hidden" name="detail_produk_id" id="detailProdukId" value="{{ $initialDetail->detail_produk_id ?? '' }}">
                <div class="w-24">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah</label>
                    <input type="number" name="jumlah" value="1" min="1" class="w-full rounded-xl border border-slate-200 px-3 py-3 text-center outline-none focus:border-brand bg-slate-50">
                </div>
                <button type="submit" class="flex-1 rounded-xl bg-brand px-6 py-3 font-bold text-white hover:bg-brand-dark transition shadow-sm">
                    Tambah ke Keranjang
                </button>
            </form>

            <div class="flex flex-wrap items-center gap-3 mb-8 pb-8 border-b border-slate-100">
                <form method="post" action="{{ route('wishlist.toggle') }}">
                    @csrf
                    <input type="hidden" name="produk_id" value="{{ $product->produk_id }}">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-brand transition">
                        <span>♡</span> Tambah ke Wishlist
                    </button>
                </form>
            </div>

            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-800">Ulasan Produk</h3>
                <div class="flex gap-2">
                    <a class="text-xs font-semibold text-brand bg-brand/10 px-3 py-1 rounded-lg hover:bg-brand/20" href="{{ route('order.rating.produk', $product->produk_id) }}">Beri Ulasan</a>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($product->ratings as $rating)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex text-amber-400 text-sm">
                                @for($i=1; $i<=5; $i++)
                                    <span>{{ $i <= $rating->bintang ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($rating->created_at)->format('d M Y') }}</span>
                        </div>
                        <div class="font-semibold text-sm text-slate-800 mb-1">{{ $rating->judul_ulasan ?? 'Ulasan Pembeli' }}</div>
                        <p class="text-sm text-slate-600">{{ $rating->isi_ulasan ?? '-' }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-100 border-dashed bg-white p-6 text-center text-slate-500 text-sm">
                        Belum ada ulasan untuk produk ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.variant-btn').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.variant-btn').forEach((item) => {
                item.className = 'variant-btn rounded-xl border px-4 py-2 text-sm font-semibold transition bg-white border-slate-200 text-slate-700 hover:border-brand/50';
            });
            button.className = 'variant-btn rounded-xl border px-4 py-2 text-sm font-semibold transition bg-brand border-brand text-white';
            document.getElementById('detailProdukId').value = button.dataset.detailId;
            document.getElementById('currentPrice').textContent = 'Rp ' + Number(button.dataset.price).toLocaleString('id-ID');
            document.getElementById('currentStock').textContent = button.dataset.stock;
        });
    });
</script>
@endsection

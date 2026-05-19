@extends('layouts.buyer')
@section('title', $product->nama_produk . ' — MOVR')
@section('content')

@php
  $galleryImages = ($product->gambarProduk ?? collect())
    ->map(function ($img) {
      return $img->url_safe;
    })
    ->filter()
    ->values()
    ->toArray();

  $activeImage = optional($product->gambarUtama)->url_safe;
  $detailProduk = ($product->detailProduk ?? collect())->values();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-400 mb-8">
    <a href="{{ route('home') }}" class="hover:text-[#63A2BB] transition">
      Beranda
    </a>
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
    </svg>
    <a href="{{ route('category.show', $product->kategori->slug) }}" 
       class="hover:text-[#63A2BB] transition">
      {{ $product->kategori->nama_kategori }}
    </a>
    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
    </svg>
    <span class="text-gray-700 font-medium line-clamp-1">
      {{ $product->nama_produk }}
    </span>
  </div>

  {{-- Product Detail Grid --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
    {{-- Left: Gallery --}}
    <div class="lg:col-span-2"
         x-data="{
           images: @js($galleryImages),
           activeImage: @js($activeImage),
           init() {
             if (!this.activeImage && $this.images.length > 0) {
               this.activeImage = $this.images[0];
             }
           }
         }">
      {{-- Main Image --}}
      <div class="mb-6 bg-gradient-to-br from-gray-50 to-gray-100 
                  rounded-3xl overflow-hidden aspect-square 
                  flex items-center justify-center relative group">
        <img :src="activeImage" 
             :alt="@json($product->nama_produk)"
             class="w-full h-full object-cover">
        
        {{-- Badge --}}
        @if($product->is_featured)
        <div class="absolute top-4 left-4 bg-[#63A2BB] 
                    text-white px-4 py-2 rounded-full 
                    text-xs font-bold uppercase">
          Unggulan
        </div>
        @endif

        {{-- Wishlist Button (Floating) --}}
        <div class="absolute top-4 right-4"
             x-data="{ 
               isWishlisted: @json(auth()->check() && \App\Models\Wishlist::where('pengguna_id', auth()->id())->where('produk_id', $product->produk_id)->exists()),
               loading: false,
               async toggle() {
                 this.loading = true;
                 const res = await fetch('{{ route('wishlist.toggle') }}', {
                   method: 'POST',
                   headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },
                   body: JSON.stringify({ produk_id: {{ $product->produk_id }} })
                 });
                 const data = await res.json();
                 if (data.success) {
                   this.isWishlisted = !this.isWishlisted;
                   showToast(this.isWishlisted ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist');
                 }
                 this.loading = false;
               }
             }">
          <button @click.prevent="toggle()"
                  :disabled="loading"
                  class="w-12 h-12 rounded-full bg-white shadow-lg 
                         flex items-center justify-center 
                         hover:scale-110 transition-transform">
            <svg class="w-6 h-6"
                 :fill="isWishlisted ? '#EF4444' : 'none'"
                 :stroke="isWishlisted ? '#EF4444' : '#63A2BB'"
                 stroke-width="2"
                 viewBox="0 0 24 24">
              <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 
                       20.364l7.682-7.682a4.5 4.5 0 00
                       -6.364-6.364L12 7.636l-1.318-1.318a4.5 
                       4.5 0 00-6.364 0z"/>
            </svg>
          </button>
        </div>
      </div>

      {{-- Thumbnails --}}
      <div class="flex gap-3 overflow-x-auto pb-2">
        <template x-for="(img, idx) in images" :key="idx">
          <button @click="activeImage = img"
                  :class="{ 
                    'ring-2 ring-[#63A2BB]': activeImage === img 
                  }"
                  class="flex-shrink-0 w-20 h-20 rounded-2xl 
                         border-2 border-gray-200 overflow-hidden
                         hover:border-[#63A2BB] transition">
            <img :src="img" 
                 class="w-full h-full object-cover">
          </button>
        </template>
      </div>
    </div>

    {{-- Right: Info & Checkout --}}
    <div x-data="{
      details: @js($detailProduk),
      selectedWarna: null,
      selectedUkuran: null,
      selectedDetail: null,
      qty: 1,
      maxQty: 1,
      loading: false,
      get warnas() {
        return [...new Map(this.details.map(d => [d.warna?.warna_id, d.warna])).values()].filter(Boolean);
      },
      get ukurans() {
        if (!this.selectedWarna) return [];
        return this.details
          .filter(d => d.warna?.warna_id === this.selectedWarna)
          .map(d => ({ ...d, id: d.detail_produk_id }));
      },
      selectWarna(warnaId) {
        this.selectedWarna = warnaId;
        this.selectedUkuran = null;
        this.selectedDetail = null;
      },
      selectUkuran(detailId) {
        this.selectedDetail = this.details.find(d => d.detail_produk_id === detailId);
        this.maxQty = this.selectedDetail?.stok ?? 0;
        this.qty = Math.min(1, this.maxQty);
      },
      async addToCart() {
        if (!this.selectedDetail) {
          alert('Pilih varian terlebih dahulu');
          return;
        }
        this.loading = true;
        const res = await fetch('{{ route('cart.add') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            detail_produk_id: this.selectedDetail.detail_produk_id,
            jumlah: this.qty
          })
        });
        const data = await res.json();
        this.loading = false;
        if (data.success) {
          showToast('Ditambahkan ke keranjang');
          this.qty = 1;
          window.dispatchEvent(new Event('cart-updated'));
        } else {
          alert(data.message || 'Gagal ditambahkan');
        }
      }
    }">
      {{-- Product Name --}}
      <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">
          {{ $product->nama_produk }}
        </h1>
        <p class="text-gray-500 text-sm">
          Kode: {{ $product->kode_produk }}
        </p>
      </div>

      {{-- Rating --}}
      <div class="flex items-center gap-3 py-4 border-b-2 border-gray-100">
        <div class="flex items-center gap-1">
          @for($i = 0; $i < 5; $i++)
            @if($i < floor($ratingStats['average'] ?? 0))
              <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 20 20">
                <path d="M9.049 2.927..."/>
              </svg>
            @else
              <svg class="w-4 h-4 text-gray-300 fill-gray-300" viewBox="0 0 20 20">
                <path d="M9.049 2.927..."/>
              </svg>
            @endif
          @endfor
        </div>
        <span class="font-bold text-sm text-gray-700">
          {{ number_format($ratingStats['average'] ?? 0, 1) }}
        </span>
        <span class="text-xs text-gray-500">
          ({{ $ratingStats['total'] ?? 0 }} ulasan)
        </span>
      </div>

      {{-- Price Section --}}
      <div class="py-4">
        @php
          $minPrice = $product->harga_dasar;
          $hasPromo = !empty($promoAktif);
          $promoNominal = 0;
          if ($hasPromo) {
            $promoNominal = (float)($promoAktif->nominal_diskon ?? 0);
            if ($promoNominal <= 0 && !empty($promoAktif->persen_diskon)) {
              $promoNominal = $minPrice * ($promoAktif->persen_diskon / 100);
            }
          }
          $hargaFinal = max(0, $minPrice - $promoNominal);
        @endphp
        <div class="flex items-baseline gap-3">
          <span class="text-3xl font-black text-[#63A2BB]">
            Rp {{ number_format($hargaFinal, 0, ',', '.') }}
          </span>
          @if($hasPromo)
            <span class="text-lg line-through text-gray-400">
              Rp {{ number_format($minPrice, 0, ',', '.') }}
            </span>
            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
              -{{ $promoAktif->persen_diskon ?? 0 }}%
            </span>
          @endif
        </div>
      </div>

      {{-- Color Selection --}}
      @if($product->detailProduk->groupBy('warna.warna_id')->count() > 1)
      <div class="py-4 border-b-2 border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-3">
          Pilih Warna
        </h3>
        <div class="flex gap-3 flex-wrap">
          <template x-for="warna in warnas" :key="warna.warna_id">
            <button @click="selectWarna(warna.warna_id)"
                    :class="{ 'ring-2 ring-[#63A2BB]': selectedWarna === warna.warna_id }"
                    class="flex items-center gap-2 px-3 py-2 rounded-full 
                           border-2 border-gray-200 hover:border-[#63A2BB] 
                           transition text-xs font-medium">
              <div class="w-5 h-5 rounded-full border-2 border-gray-300"
                   :style="{ backgroundColor: warna.kode_hex }">
              </div>
              <span x-text="warna.nama_warna"></span>
            </button>
          </template>
        </div>
      </div>
      @endif

      {{-- Size Selection --}}
      <div class="py-4 border-b-2 border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-3">
          Pilih Ukuran
        </h3>
        <div class="flex gap-2 flex-wrap">
          <template x-for="ukuran in ukurans" :key="ukuran.detail_produk_id">
            <button @click="selectUkuran(ukuran.detail_produk_id)"
                    :disabled="ukuran.stok === 0"
                    :class="{ 
                      'bg-[#63A2BB] text-white ring-2 ring-[#63A2BB]': selectedDetail?.detail_produk_id === ukuran.detail_produk_id,
                      'opacity-50 cursor-not-allowed': ukuran.stok === 0
                    }"
                    class="px-4 py-2.5 border-2 border-gray-200 rounded-lg
                           text-sm font-bold hover:border-[#63A2BB] transition">
              <span x-text="ukuran.ukuran"></span>
              <span x-show="ukuran.stok === 0" class="text-xs">
                (Habis)
              </span>
            </button>
          </template>
        </div>
      </div>

      {{-- Quantity --}}
      <div class="py-4 border-b-2 border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-3">
          Jumlah
        </h3>
        <div class="flex items-center gap-3">
          <div class="flex items-center border-2 border-gray-200 rounded-lg">
            <button @click="qty = Math.max(1, qty - 1)"
                    :disabled="qty <= 1"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition">
              −
            </button>
            <input type="number" x-model.number="qty" 
                   :max="maxQty" min="1"
                   class="w-12 text-center border-l border-r 
                          border-gray-200 py-2 focus:outline-none 
                          focus:ring-2 focus:ring-[#63A2BB]/20">
            <button @click="qty = Math.min(maxQty, qty + 1)"
                    :disabled="qty >= maxQty"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition">
              +
            </button>
          </div>
          <span class="text-xs text-gray-500">
            Stok: <span x-text="maxQty"></span>
          </span>
        </div>
      </div>

      {{-- Action Buttons --}}
      <div class="flex gap-3 pt-6">
        <button @click="addToCart()"
                :disabled="loading || !selectedDetail || maxQty === 0"
                class="flex-1 bg-[#63A2BB] text-white font-bold py-3.5 
                       rounded-2xl hover:shadow-lg hover:-translate-y-1 
                       transition-all disabled:opacity-50 disabled:cursor-not-allowed">
          <span x-show="!loading">Tambah ke Keranjang</span>
          <span x-show="loading">Menambahkan...</span>
        </button>
        <a href="{{ route('checkout.index') }}"
           class="flex-1 border-2 border-[#63A2BB] text-[#63A2BB] 
                  font-bold py-3.5 rounded-2xl hover:bg-[#63A2BB]/5 
                  transition text-center">
          Beli Sekarang
        </a>
      </div>
    </div>
  </div>

  {{-- Product Tabs --}}
  <div class="mt-16 bg-white rounded-3xl overflow-hidden shadow-sm"
       x-data="{ activeTab: 'description' }">
    {{-- Tab Buttons --}}
    <div class="flex border-b-2 border-gray-100 overflow-x-auto">
      <button @click="activeTab = 'description'"
              :class="{ 
                'text-[#63A2BB] border-b-2 border-[#63A2BB]': activeTab === 'description',
                'text-gray-500': activeTab !== 'description'
              }"
              class="px-6 py-4 text-sm font-bold transition">
        Deskripsi
      </button>
      <button @click="activeTab = 'specification'"
              :class="{ 
                'text-[#63A2BB] border-b-2 border-[#63A2BB]': activeTab === 'specification',
                'text-gray-500': activeTab !== 'specification'
              }"
              class="px-6 py-4 text-sm font-bold transition">
        Spesifikasi
      </button>
      <button @click="activeTab = 'reviews'"
              :class="{ 
                'text-[#63A2BB] border-b-2 border-[#63A2BB]': activeTab === 'reviews',
                'text-gray-500': activeTab !== 'reviews'
              }"
              class="px-6 py-4 text-sm font-bold transition">
        Ulasan ({{ $ratingStats['total'] ?? 0 }})
      </button>
    </div>

    {{-- Tab Content --}}
    <div class="p-8">
      {{-- Deskripsi --}}
      <div x-show="activeTab === 'description'" class="prose prose-sm max-w-none">
        <div class="text-gray-600 leading-relaxed whitespace-pre-wrap">
          {!! nl2br(e($product->deskripsi ?? '')) !!}
        </div>
      </div>

      {{-- Spesifikasi --}}
      <div x-show="activeTab === 'specification'">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Kode Produk
              </p>
              <p class="text-gray-900 font-semibold">
                {{ $product->kode_produk }}
              </p>
            </div>
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Kategori
              </p>
              <p class="text-gray-900 font-semibold">
                {{ $product->kategori->nama_kategori ?? '-' }}
              </p>
            </div>
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Gender
              </p>
              <p class="text-gray-900 font-semibold capitalize">
                {{ $product->gender ?? 'Unisex' }}
              </p>
            </div>
          </div>
          <div class="space-y-4">
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Tipe Olahraga
              </p>
              <p class="text-gray-900 font-semibold">
                {{ $product->tipe_olahraga ?? '-' }}
              </p>
            </div>
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Berat
              </p>
              <p class="text-gray-900 font-semibold">
                {{ $product->detailProduk->first()?->berat_gram ?? '0' }} gram
              </p>
            </div>
            <div class="pb-4 border-b border-gray-100">
              <p class="text-xs font-bold text-gray-500 uppercase mb-1">
                Stok Total
              </p>
              <p class="text-gray-900 font-semibold">
                {{ $product->detailProduk->sum('stok') }} unit
              </p>
            </div>
          </div>
        </div>
      </div>

      {{-- Ulasan --}}
      <div x-show="activeTab === 'reviews'">
        @include('components.product-reviews', [
          'ratingStats' => $ratingStats,
          'product' => $product,
          'hasPurchased' => $hasPurchased ?? false,
          'hasReviewed' => $hasReviewed ?? false
        ])
      </div>
    </div>
  </div>

  {{-- Similar Products --}}
  <div class="mt-16">
    <h2 class="text-2xl font-black text-gray-900 mb-8">
      Produk Serupa
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
      @forelse($similarProducts ?? [] as $item)
        <x-product-card :produk="$item" />
      @empty
        <div class="col-span-full text-center py-12 text-gray-500">
          Produk serupa tidak ditemukan
        </div>
      @endforelse
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
    // Event listener untuk update cart badge
    window.addEventListener('cart-updated', () => {
        // Reload cart count badge di header
        fetch('/api/cart-count')
            .then(r => r.json())
            .then(data => {
                document.querySelector('[data-cart-badge]').textContent = data.count;
            });
    });
</script>
@endsection

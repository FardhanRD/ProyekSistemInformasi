@extends('layouts.buyer')
@section('title', $product->nama_produk . ' — MOVR')
@section('content')

@php
  $galleryImages = collect($product->images ?? $product->gambarProduk ?? [])
    ->map(function ($img) {
      return $img->url_lengkap ?? $img->url_safe ?? null;
    })
    ->filter()
    ->values()
    ->all();

  $activeImage = $galleryImages[0]
    ?? optional($product->gambarUtama)->url_lengkap
    ?? optional($product->gambarUtama)->url_safe
    ?? asset('images/placeholder.png');

  $variantItems = collect($product->details ?? $product->detailProduk ?? [])
    ->map(function ($detail) {
      return [
        'detail_produk_id' => $detail->detail_produk_id,
        'warna_id' => $detail->warna_id,
        'nama_warna' => $detail->warna?->nama_warna,
        'kode_hex' => $detail->warna?->kode_hex,
        'ukuran' => $detail->ukuran,
        'stok' => (int) ($detail->stok ?? 0),
        'harga' => (float) ($detail->harga ?? 0),
      ];
    })
    ->values();

  $warnaOptions = $variantItems
    ->filter(fn ($item) => !is_null($item['warna_id']))
    ->unique('warna_id')
    ->values();

  $hasWarna = $warnaOptions->isNotEmpty();
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
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-12 items-start">
    {{-- Left: Gallery --}}
    <div class="lg:col-span-1"
         x-data="{
           images: @js($galleryImages),
           activeImage: @js($activeImage),
           init() {
             if (!this.activeImage && this.images.length > 0) {
               this.activeImage = this.images[0];
             }
           }
         }">
      {{-- Main Image --}}
      <div class="relative bg-[#F8FAFB] rounded-2xl overflow-hidden h-[380px] sm:h-[460px] md:h-[520px] flex items-center justify-center group">
        <img :src="activeImage"
             :alt="@json($product->nama_produk)"
             class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
        
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

      <div class="grid grid-cols-4 gap-2 mt-3">
        <template x-for="(img, idx) in images" :key="idx">
          <button type="button"
                  @click="activeImage = img"
                  :class="{
                    'ring-2 ring-[#63A2BB] border-[#63A2BB]': activeImage === img
                  }"
                  class="h-16 rounded-xl overflow-hidden bg-gray-50 border-2 border-gray-200 hover:border-[#63A2BB] transition">
            <img :src="img"
                 class="w-full h-full object-cover object-center">
          </button>
        </template>
      </div>
    </div>

    {{-- Right: Info & Checkout --}}
    <div class="lg:col-span-1 flex flex-col gap-4"
         x-data="productDetailState(@js($variantItems), @js($warnaOptions), @js($hasWarna))">
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
      @if($hasWarna)
      <div class="py-4 border-b-2 border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-1">
          Pilih Warna
        </h3>
        <p class="text-xs text-gray-500 mb-3">
          Pilih varian yang tersedia sebelum menentukan ukuran.
        </p>
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
        <h3 class="text-sm font-bold text-gray-900 mb-1">
          Pilih Ukuran
        </h3>
        <p class="text-xs text-gray-500 mb-3">
          Stok per ukuran ditampilkan di bawah nama ukuran.
        </p>
        <div class="flex gap-2 flex-wrap">
          <template x-for="ukuran in ukurans" :key="ukuran.detail_produk_id">
            <button @click="selectUkuran(ukuran.detail_produk_id)"
                    :disabled="ukuran.stok === 0"
                    :class="{ 
                      'bg-[#63A2BB] text-white ring-2 ring-[#63A2BB]': selectedDetail && selectedDetail.detail_produk_id === ukuran.detail_produk_id,
                      'opacity-50 cursor-not-allowed': ukuran.stok === 0
                    }"
                    class="min-w-[76px] px-4 py-2.5 border-2 border-gray-200 rounded-lg
                           text-sm font-bold hover:border-[#63A2BB] transition flex flex-col items-center leading-tight">
              <span x-text="ukuran.ukuran"></span>
              <span class="text-xs font-medium mt-1" x-text="ukuran.stok > 0 ? `${ukuran.stok} stok` : 'Habis'"></span>
            </button>
          </template>
        </div>
      </div>

      <div class="py-4 border-b-2 border-gray-100" x-show="selectedDetail !== null" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="text-sm font-bold text-gray-700 mb-3">
          Jumlah
        </h3>
        <div class="flex items-center gap-4">
          <div class="flex items-center border-2 border-gray-200 rounded-2xl overflow-hidden bg-white">
            <button type="button" @click="qty = Math.max(1, qty - 1)"
                    class="w-11 h-11 flex items-center justify-center text-gray-500 hover:bg-[#63A2BB]/10 hover:text-[#63A2BB] font-bold text-xl transition disabled:opacity-30 disabled:cursor-not-allowed"
                    :disabled="qty <= 1">
              −
            </button>
            <span x-text="qty" class="w-12 text-center font-bold text-gray-800 text-base"></span>
            <button type="button" @click="qty = Math.min(maxQty, qty + 1)"
                    class="w-11 h-11 flex items-center justify-center text-gray-500 hover:bg-[#63A2BB]/10 hover:text-[#63A2BB] font-bold text-xl transition disabled:opacity-30 disabled:cursor-not-allowed"
                    :disabled="qty >= maxQty">
              +
            </button>
          </div>
          <span class="text-sm text-gray-400">
            Maks <span x-text="maxQty" class="font-semibold"></span> pcs
          </span>
        </div>
      </div>

      {{-- Action Buttons --}}
      <div class="flex gap-3 mt-6">
        <button type="button"
                @click="addToCart()"
                :disabled="!selectedDetail || loading"
                :class="(!selectedDetail || loading)
                  ? 'opacity-60 cursor-not-allowed bg-[#63A2BB]'
                  : 'bg-[#63A2BB] hover:-translate-y-1 hover:shadow-lg hover:shadow-[#63A2BB]/30 hover:bg-[#4A8BA3]'"
                class="flex-1 text-white py-4 px-6 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200">
          <svg x-show="loading" class="animate-spin w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <svg x-show="!loading" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          <span x-text="loading ? 'Menambahkan...' : 'Tambah ke Keranjang'"></span>
        </button>

        <button type="button"
                @click="buyNow()"
                :disabled="!selectedDetail || loading"
                :class="(!selectedDetail || loading)
                  ? 'opacity-60 cursor-not-allowed'
                  : 'hover:-translate-y-1 hover:shadow-lg hover:bg-gray-800'"
                class="flex-1 bg-gray-900 text-white py-4 px-6 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          Beli Sekarang
        </button>
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
  window.productDetailState = function (variantItems, warnaOptions, hasWarna) {
    return {
      variantItems: variantItems,
      warnaOptions: warnaOptions,
      hasWarna: hasWarna,
      selectedWarna: null,
      selectedUkuran: null,
      selectedDetail: null,
      qty: 1,
      maxQty: 1,
      loading: false,

      init() {
        if (this.hasWarna && this.warnaOptions.length > 0) {
          this.selectedWarna = this.warnaOptions[0].warna_id;
        } else {
          this.selectedWarna = '__no_color__';
        }

        this.$nextTick(() => {
          if (this.ukurans.length === 1) {
            this.selectUkuran(this.ukurans[0].detail_produk_id);
          }
        });
      },

      get warnas() {
        return this.warnaOptions;
      },

      get ukurans() {
        if (!this.hasWarna || this.selectedWarna === '__no_color__') return this.variantItems;
        if (!this.selectedWarna) return [];
        return this.variantItems.filter(item => item.warna_id === this.selectedWarna);
      },

      selectWarna(warnaId) {
        this.selectedWarna = warnaId;
        this.selectedUkuran = null;
        this.selectedDetail = null;
        this.qty = 1;
        this.maxQty = 1;
      },

      selectUkuran(detailId) {
        this.selectedDetail = this.variantItems.find(item => item.detail_produk_id === detailId);
        this.selectedUkuran = this.selectedDetail && this.selectedDetail.ukuran ? this.selectedDetail.ukuran : null;
        this.maxQty = this.selectedDetail && this.selectedDetail.stok ? this.selectedDetail.stok : 1;
        this.qty = Math.max(1, Math.min(this.qty, this.maxQty || 1));
      },

      get selectedVariantText() {
        if (!this.selectedDetail) return 'Pilih ukuran';
        const parts = [];
        if (this.hasWarna && this.selectedDetail.nama_warna) {
          parts.push(this.selectedDetail.nama_warna);
        }
        if (this.selectedDetail.ukuran) {
          parts.push(this.selectedDetail.ukuran);
        }
        return parts.length ? parts.join(' / ') : 'Varian terpilih';
      },

      get totalStock() {
        return this.variantItems.reduce((sum, item) => sum + (Number(item.stok) || 0), 0);
      },

      async addToCart() {
        if (!this.selectedDetail) {
          showToast('Pilih ukuran dulu', 'warning');
          return false;
        }

        this.loading = true;
        try {
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
          if (data.success) {
            showToast('Ditambahkan ke keranjang');
            window.dispatchEvent(new Event('cart-updated'));
            return true;
          }

          alert(data.message || 'Gagal ditambahkan');
          return false;
        } finally {
          this.loading = false;
        }
      },

      async buyNow() {
        if (!this.selectedDetail) {
          showToast('Pilih ukuran dulu', 'warning');
          return;
        }

        const added = await this.addToCart();
        if (added) {
          window.location.href = '{{ route('cart.index') }}';
        }
      }
    };
  };

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

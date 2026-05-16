@extends('layouts.buyer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-8 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a> 
            <span class="mx-2">/</span>
            <a href="{{ route('category.show', $product->kategori->slug) }}" class="hover:text-blue-600">
                {{ $product->kategori->nama_kategori }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $product->nama_produk }}</span>
        </div>

        <!-- Product Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            <!-- Left: Gallery -->
            <div x-data="galleryComponent()" class="flex flex-col gap-4">
                <!-- Main Image -->
                <div class="bg-white rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center" style="height: 500px;">
                    <img :src="activeImage" 
                         :alt="activeImageAlt"
                         class="max-w-full max-h-full object-contain">
                </div>

                <!-- Thumbnails -->
                <div class="flex gap-3 overflow-x-auto pb-2">
                    <template x-for="(image, index) in images" :key="index">
                        <button @click="selectImage(image, `Gambar ${index + 1}`)"
                                :class="{'ring-2 ring-blue-500': activeImage === image}"
                                class="flex-shrink-0 w-20 h-20 rounded border-2 border-gray-200 p-1 hover:border-blue-500 transition">
                            <img :src="image" 
                                 class="w-full h-full object-cover rounded">
                        </button>
                    </template>
                </div>

                <script>
                    function galleryComponent() {
                        return {
                            images: @json(($product->images ?? collect())->map(function($img){ return $img->url_lengkap ?? asset('storage/' . $img->url_gambar); })->values()->toArray()),
                            activeImage: @json(optional($product->images->first())->url_lengkap ?? (optional($product->images->first())->url_gambar ? asset('storage/' . $product->images->first()->url_gambar) : asset('images/default-product.svg'))),
                            activeImageAlt: 'Gambar Produk',
                            selectImage(image, alt) {
                                this.activeImage = image;
                                this.activeImageAlt = alt;
                            }
                        }
                    }
                </script>
            </div>

            <!-- Right: Product Info -->
            <div x-data="productComponent()" class="flex flex-col gap-6">
                <!-- Name -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->nama_produk }}</h1>
                    
                    <!-- Rating -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($ratingStats['average']))
                                    <span class="text-lg text-yellow-400">★</span>
                                @elseif($i - 0.5 <= $ratingStats['average'])
                                    <span class="text-lg text-yellow-400">⭐</span>
                                @else
                                    <span class="text-lg text-gray-300">☆</span>
                                @endif
                            @endfor
                        </div>
                        <span class="font-semibold text-gray-900">{{ $ratingStats['average'] }}</span>
                        <span class="text-sm text-gray-600">({{ $ratingStats['total'] }} ulasan)</span>
                        <a href="#reviews" class="text-blue-600 hover:underline text-sm">Lihat ulasan</a>
                    </div>
                </div>

                <!-- Price -->
                <div class="border-t border-gray-200 pt-4">
                    @php
                        $minPrice = $product->details->min('harga') ?? $product->harga_dasar;
                        $hasPromo = !empty($promoAktif);
                        $promoNominal = 0;
                        if ($hasPromo) {
                            $promoNominal = (float) ($promoAktif->nominal_diskon ?? 0);
                            if ($promoNominal <= 0 && !empty($promoAktif->persen_diskon)) {
                                $promoNominal = ((float) $minPrice) * ((float) $promoAktif->persen_diskon) / 100;
                            }
                        }
                        $hargaFinal = max(0, (float) $minPrice - $promoNominal);
                    @endphp
                    
                    <div class="flex items-baseline gap-3">
                        @if($hasPromo)
                            <span class="text-2xl font-bold text-red-600">Rp {{ number_format($hargaFinal, 0, ',', '.') }}</span>
                            <span class="text-lg line-through text-gray-500">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-sm font-semibold">HEMAT Rp {{ number_format($promoNominal, 0, ',', '.') }}</span>
                        @else
                            <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Color Selection -->
                @if($product->details->groupBy('warna_id')->count() > 1)
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Pilih Warna</h3>
                    <div class="flex gap-3 flex-wrap">
                        <template x-for="(warna, warnaId) in uniqueWarnas" :key="warnaId">
                            <button @click="selectColor(warnaId)"
                                    :class="{'ring-2 ring-blue-500': selectedColor === warnaId}"
                                    class="flex items-center gap-2 px-4 py-2 border-2 border-gray-200 rounded hover:border-blue-500 transition"
                                    :title="warna.nama_warna">
                                <span :style="{backgroundColor: warna.kode_hex, width: '24px', height: '24px'}" 
                                      class="rounded border border-gray-300"></span>
                                <span class="text-sm text-gray-700" x-text="warna.nama_warna"></span>
                            </button>
                        </template>
                    </div>
                </div>
                @endif

                <!-- Size Selection -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Pilih Ukuran</h3>
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="(ukuran, idx) in availableSizes" :key="idx">
                            <button @click="selectedSize === ukuran.detail_produk_id ? selectedSize = null : selectedSize = ukuran.detail_produk_id"
                                    :disabled="ukuran.stok === 0"
                                    :class="{
                                        'ring-2 ring-blue-500 bg-blue-50': selectedSize === ukuran.detail_produk_id,
                                        'opacity-50 cursor-not-allowed line-through': ukuran.stok === 0
                                    }"
                                    class="px-4 py-2 border-2 border-gray-200 rounded font-medium hover:border-blue-500 transition"
                                    x-text="`${ukuran.ukuran}${ukuran.stok === 0 ? ' (Habis)' : ''}`">
                            </button>
                        </template>
                    </div>
                    <template x-if="errorMessage">
                        <p class="text-red-600 text-sm mt-2" x-text="errorMessage"></p>
                    </template>
                </div>

                <!-- Quantity -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Jumlah</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-gray-300 rounded">
                            <button @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2 hover:bg-gray-100">−</button>
                            <input type="number" x-model.number="quantity" min="1" :max="maxQuantity"
                                   class="w-16 text-center border-l border-r border-gray-300 py-2 focus:outline-none">
                            <button @click="quantity = Math.min(maxQuantity, quantity + 1)" class="px-4 py-2 hover:bg-gray-100">+</button>
                        </div>
                        <span class="text-sm text-gray-600" x-text="`Stok: ${maxQuantity}`"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <!-- Wishlist Button -->
                    <button @click="toggleWishlist()"
                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-300 rounded-lg hover:border-red-500 hover:bg-red-50 transition flex-shrink-0">
                        <span x-text="isWishlisted ? '❤' : '🤍'" class="text-2xl"></span>
                    </button>

                    <!-- Add to Cart Button -->
                    <button @click="addToCart()"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Tambah ke Keranjang
                    </button>
                </div>

                <!-- Buy Now Button -->
                <button @click="addToCartAndCheckout()"
                        class="w-full bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    Beli Sekarang
                </button>
            </div>

            <script>
                function productComponent() {
                    return {
                        details: @json($product->details->toArray()),
                        selectedColor: null,
                        selectedSize: null,
                        quantity: 1,
                        maxQuantity: @json($product->details->first()?->stok ?? 1),
                        errorMessage: '',
                        isWishlisted: @json(
                            auth()->check() &&
                            ($wishlistOwnerId = \App\Models\Wishlist::resolveOwnerId(auth()->user())) &&
                            \App\Models\Wishlist::where(\App\Models\Wishlist::ownerColumn(), $wishlistOwnerId)->where('produk_id', $product->produk_id)->exists()
                        ),
                        uniqueWarnas: @json($product->details->unique('warna_id')->map(fn($d) => $d->warna)->values()->keyBy('warna_id')->toArray()),
                        
                        get availableSizes() {
                            let filtered = this.details;
                            if (this.selectedColor) {
                                filtered = filtered.filter(d => d.warna_id == this.selectedColor);
                            }
                            return filtered;
                        },

                        selectColor(warnaId) {
                            this.selectedColor = warnaId;
                            this.selectedSize = null;
                            this.errorMessage = '';
                            
                            // Update image jika ada variant warna
                            let variantDetail = this.details.find(d => d.warna_id == warnaId);
                            if (variantDetail?.warna?.gambar_warna) {
                                // Update gallery image
                                document.dispatchEvent(new CustomEvent('color-changed', {
                                    detail: { imagePath: variantDetail.warna.gambar_warna }
                                }));
                            }
                        },

                        toggleWishlist() {
                            @if(!auth()->check())
                                window.location.href = "{{ route('login') }}";
                                return;
                            @endif

                            const endpoint = this.isWishlisted
                                ? '/wishlist/remove?produk_id={{ $product->produk_id }}'
                                : '/wishlist/add';
                            const payload = new FormData();
                            payload.append('produk_id', {{ $product->produk_id }});

                            fetch(endpoint, {
                                method: this.isWishlisted ? 'DELETE' : 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: this.isWishlisted ? null : payload
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    this.isWishlisted = !this.isWishlisted;
                                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                                }
                            });
                        },

                        async addToCart() {
                            this.errorMessage = '';

                            if (Object.keys(this.uniqueWarnas).length > 1 && !this.selectedColor) {
                                this.errorMessage = 'Pilih warna terlebih dahulu';
                                return;
                            }
                            
                            if (!this.selectedSize) {
                                this.errorMessage = 'Pilih ukuran terlebih dahulu';
                                return;
                            }

                            @if(!auth()->check())
                                window.location.href = "{{ route('login') }}";
                                return;
                            @endif

                            const response = await fetch('/cart/add', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: (() => {
                                    const fd = new FormData();
                                    fd.append('detail_produk_id', this.selectedSize);
                                    fd.append('jumlah', this.quantity);
                                    return fd;
                                })()
                            });

                            const data = await response.json();
                            if (data.success) {
                                alert('✓ Produk berhasil ditambahkan ke keranjang');
                                window.dispatchEvent(new CustomEvent('cart-updated'));
                                return true;
                            }

                            this.errorMessage = data.message || 'Gagal menambahkan ke keranjang';
                            return false;
                        },

                        async addToCartAndCheckout() {
                            const success = await this.addToCart();
                            if (success) {
                                window.location.href = "{{ route('checkout.index') }}";
                            }
                        }
                    }
                }
            </script>
        </div>

        <!-- Product Info Tabs -->
        <div class="bg-white rounded-lg border border-gray-200 mb-16">
            <div x-data="{ activeTab: 'description' }" class="flex flex-col">
                <!-- Tab Buttons -->
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'description'"
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'description', 'text-gray-600': activeTab !== 'description'}"
                            class="px-6 py-4 font-semibold transition hover:text-blue-600">
                        Deskripsi
                    </button>
                    <button @click="activeTab = 'specification'"
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'specification', 'text-gray-600': activeTab !== 'specification'}"
                            class="px-6 py-4 font-semibold transition hover:text-blue-600">
                        Spesifikasi
                    </button>
                    <button @click="activeTab = 'reviews'"
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'reviews', 'text-gray-600': activeTab !== 'reviews'}"
                            class="px-6 py-4 font-semibold transition hover:text-blue-600">
                        Ulasan ({{ $ratingStats['total'] }})
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Description -->
                    <div x-show="activeTab === 'description'" class="prose max-w-none">
                        {!! nl2br(e($product->deskripsi)) !!}
                    </div>

                    <!-- Specification -->
                    <div x-show="activeTab === 'specification'" class="space-y-4">
                        @php
                            $specs = [
                                'Spesifikasi' => $product->spesifikasi ?? '-',
                                'Gender' => ucfirst($product->gender ?? 'unisex'),
                                'Tipe Olahraga' => $product->tipe_olahraga ?? '-',
                                'Berat' => ($product->details->first()?->berat_gram ?? 0) . 'g',
                            ];
                        @endphp
                        
                        @foreach($specs as $key => $value)
                            <div class="flex border-b pb-3">
                                <span class="w-32 font-semibold text-gray-700">{{ $key }}</span>
                                <span class="text-gray-600">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Reviews Tab Content -->
                    <div id="reviews" x-show="activeTab === 'reviews'">
                        @include('components.product-reviews', ['ratingStats' => $ratingStats, 'product' => $product, 'hasPurchased' => $hasPurchased, 'hasReviewed' => $hasReviewed])
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Products -->
        @if($similarProducts->count() > 0)
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Serupa</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($similarProducts as $similar)
                    <x-product-card :produk="$similar" :showWishlistBtn="true" />
                @endforeach
            </div>
        </div>
        @endif
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

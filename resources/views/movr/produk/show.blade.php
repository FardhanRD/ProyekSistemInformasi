@extends('movr.layouts.app')

@section('content')

    {{-- BREADCRUMB: Clean & Minimalist --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wide" aria-label="Breadcrumb">
                <ol class="inline-flex items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li><a href="{{ route('produk.index') }}" class="hover:text-black transition">Store</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li class="text-black truncate max-w-[200px]">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                {{-- KOLOM KIRI: IMAGE GALLERY (7 Kolom) --}}
                <div class="lg:col-span-7">
                    @php
                        $galleryImages = $product->images->pluck('image_path')->filter()->values();
                        if ($galleryImages->isEmpty() && $product->image) {
                            $galleryImages = collect([$product->image]);
                        }
                    @endphp

                    {{-- Main Image Display --}}
                    <div class="w-full bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 mb-4 relative group">
                        @if ($galleryImages->isNotEmpty())
                            <img id="main-image" src="{{ asset('storage/' . $galleryImages->first()) }}" alt="{{ $product->name }}"
                                class="w-full h-auto object-contain max-h-[600px] mix-blend-multiply mx-auto transition-transform duration-500 group-hover:scale-105 cursor-zoom-in">
                        @else
                            <div class="w-full h-[500px] flex items-center justify-center text-gray-300">
                                <i class="fas fa-image text-6xl opacity-20"></i>
                            </div>
                        @endif

                        {{-- Status Badge --}}
                        @if ($product->stock <= 0)
                            <span
                                class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Sold
                                Out</span>
                        @elseif($product->stock < 5)
                            <span
                                class="absolute top-4 left-4 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Low
                                Stock</span>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    <div class="grid grid-cols-6 gap-3">
                        @forelse($galleryImages as $img)
                            <button onclick="switchImage(this)"
                                class="thumbnail {{ $loop->first ? 'active ring-2 ring-black ring-offset-1' : '' }} rounded-lg overflow-hidden bg-gray-50 aspect-square border border-gray-100">
                                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                            </button>
                        @empty
                            <div class="rounded-lg overflow-hidden bg-gray-50 border border-gray-100 aspect-square flex items-center justify-center opacity-50">
                                <i class="fas fa-image text-gray-300"></i>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- KOLOM KANAN: PRODUCT INFO (Sticky Sidebar) (5 Kolom) --}}
                <div class="lg:col-span-5">
                    <div class="sticky top-8">

                        {{-- Header Info --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">
                                    {{ $product->category->name ?? 'General' }}
                                </span>
                                @if ($product->ulasan->count() > 0)
                                    <div
                                        class="flex items-center gap-1 text-xs font-bold text-gray-900 border-l border-gray-300 pl-2">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        {{ number_format($product->ulasan->avg('rating'), 1) }}
                                    </div>
                                @endif
                            </div>

                            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-4">
                                {{ $product->name }}
                            </h1>

                            <div class="pb-6 border-b border-gray-100">
                                <p class="text-3xl font-bold text-gray-900 mb-3">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                {{-- Like Button (Bawah Harga) --}}
                                @auth
                                    <button type="button" onclick="toggleFavorite({{ $product->id }})" id="fav-btn"
                                        class="text-gray-400 hover:text-blue-500 transition text-2xl">
                                        <i class="far fa-heart"></i>
                                    </button>
                                @endauth
                            </div>
                        </div>

                        {{-- Action Form (UPDATED LAYOUT) --}}
                        <form action="{{ route('keranjang.store') }}" method="POST" class="mb-8">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $product->id }}">

                            {{-- Quantity Selector --}}
                            <div class="mb-6">
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantity</label>
                                <div class="inline-flex items-center border border-gray-300 rounded-lg h-12 w-32">
                                    <button type="button" onclick="updateQty(-1)"
                                        class="w-10 h-full flex items-center justify-center hover:bg-gray-50 transition text-gray-600 rounded-l-lg">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" name="jumlah" id="qty" value="1" min="1"
                                        max="{{ $product->stock }}"
                                        class="w-full h-full text-center border-none focus:ring-0 font-bold text-gray-900 p-0"
                                        readonly>
                                    <button type="button" onclick="updateQty(1)"
                                        class="w-10 h-full flex items-center justify-center hover:bg-gray-50 transition text-gray-600 rounded-r-lg">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <span class="text-xs text-gray-400 ml-3">
                                    {{ $product->stock }} items available
                                </span>
                            </div>

                            {{-- BUTTONS: GRID LAYOUT (Modern & Clean) --}}
                            <div class="flex flex-col gap-3">
                                {{-- Row 1: Add to Cart & Buy Now --}}
                                <div class="grid grid-cols-2 gap-3">
                                    {{-- Tombol 1: Add to Cart (Secondary / Outline) --}}
                                    <button type="submit" name="action" value="cart"
                                        class="flex items-center justify-center gap-2 border-2 border-gray-200 hover:border-black text-gray-700 hover:text-black py-4 rounded-xl font-bold uppercase tracking-widest text-xs transition-all duration-300 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed border-gray-100' : '' }}"
                                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-shopping-bag"></i>
                                        <span class="hidden sm:inline">Add to Cart</span>
                                        <span class="sm:hidden">Cart</span>
                                    </button>

                                    {{-- Tombol 2: Buy Now (Primary / Solid) --}}
                                    <button type="submit" name="action" value="checkout"
                                        class="flex items-center justify-center gap-2 bg-black text-white py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition shadow-xl shadow-gray-200 {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                        <span>{{ $product->stock > 0 ? 'Buy Now' : 'Out of Stock' }}</span>
                                        @if ($product->stock > 0)
                                            <i class="fas fa-arrow-right"></i>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- TABS: Description & Reviews (Clean Organization) --}}
                        <div class="border rounded-xl border-gray-200 overflow-hidden">
                            {{-- Tab Headers --}}
                            <div class="flex border-b border-gray-200 bg-gray-50">
                                <button onclick="openTab(event, 'desc')"
                                    class="tab-link flex-1 py-3 text-xs font-bold uppercase tracking-wider text-black border-b-2 border-black bg-white">
                                    Description
                                </button>
                                <button onclick="openTab(event, 'reviews')"
                                    class="tab-link flex-1 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-black transition">
                                    Reviews ({{ $product->ulasan->count() }})
                                </button>
                            </div>

                            {{-- Tab Content: Description --}}
                            <div id="desc" class="tab-content p-6 bg-white block">
                                <div class="prose prose-sm text-gray-600 max-w-none leading-relaxed">
                                    <p>{{ $product->description }}</p>
                                </div>
                                <div class="mt-6 pt-6 border-t border-gray-100 flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase">Sold By</p>
                                        <p class="text-sm font-bold text-black">
                                            {{ $product->penjual->name ?? 'Official Store' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab Content: Reviews --}}
                            <div id="reviews" class="tab-content p-6 bg-white hidden">
                                @auth
                                    <form action="{{ route('ulasan.store') }}" method="POST" class="mb-6">
                                        @csrf
                                        <input type="hidden" name="produk_id" value="{{ $product->id }}">
                                        <div class="mb-2">
                                            <label class="text-xs font-bold text-gray-900">Rating</label>
                                            <div class="flex gap-1 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <input type="radio" id="r{{ $i }}" name="rating"
                                                        value="{{ $i }}" class="hidden peer" required>
                                                    <label for="r{{ $i }}"
                                                        class="text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400 text-lg cursor-pointer">★</label>
                                                @endfor
                                            </div>
                                        </div>
                                        <textarea name="komentar" rows="2"
                                            class="w-full border border-gray-200 rounded-lg p-2 text-sm focus:border-black focus:ring-0"
                                            placeholder="Write your review..."></textarea>
                                        <button
                                            class="mt-2 bg-black text-white text-xs font-bold px-4 py-2 rounded-lg">Post</button>
                                    </form>
                                @endauth

                                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                    @forelse($ulasan as $review)
                                        <div class="border-b border-gray-100 last:border-0 pb-4 last:pb-0">
                                            <div class="flex justify-between mb-1">
                                                <span class="font-bold text-xs">{{ $review->pembeli->name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="text-[10px] text-yellow-400 mb-1">
                                                @for ($k = 0; $k < $review->rating; $k++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                            </div>
                                            <p class="text-xs text-gray-600">{{ $review->komentar }}</p>
                                        </div>
                                    @empty
                                        <p class="text-center text-gray-400 text-xs py-4">No reviews yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- RELATED PRODUCTS (Clean Grid) --}}
    @if ($relatedProducts->count() > 0)
        <section class="bg-gray-50 py-16 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex justify-between items-end mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">You Might Also Like</h3>
                    <a href="{{ route('produk.index') }}"
                        class="text-sm font-bold text-gray-900 hover:text-gray-600 underline decoration-gray-300 underline-offset-4">View
                        All</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $item)
                        <a href="{{ route('produk.show', $item->id) }}"
                            class="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:border-gray-300 transition-all duration-300 hover:shadow-lg hover:shadow-gray-100">
                            <div class="aspect-[4/5] bg-gray-100 overflow-hidden relative">
                                @if ($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        class="w-full h-full object-cover mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300"><i
                                            class="fas fa-image"></i></div>
                                @endif

                                {{-- Quick Add Button (Visible on Hover) --}}
                                <div
                                    class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0 duration-300">
                                    <div
                                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md text-black">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ $item->category->name ?? 'Item' }}</p>
                                <h4 class="font-bold text-gray-900 text-sm truncate mb-2">{{ $item->name }}</h4>
                                <p class="font-bold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <script>
        // 1. Image Switcher
        function switchImage(el) {
            document.querySelectorAll('.thumbnail').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-black', 'ring-offset-1');
            });
            el.classList.add('ring-2', 'ring-black', 'ring-offset-1');

            const src = el.querySelector('img').src;
            const mainImg = document.getElementById('main-image');
            mainImg.style.opacity = 0;
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = 1;
            }, 200);
        }

        // 2. Quantity Logic
        function updateQty(change) {
            const input = document.getElementById('qty');
            const max = parseInt(input.getAttribute('max'));
            let val = parseInt(input.value);
            val += change;
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
        }

        // 3. Tab Logic
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
                tabcontent[i].classList.remove('block');
                tabcontent[i].classList.add('hidden');
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("border-black", "text-black", "bg-white");
                tablinks[i].classList.add("text-gray-500", "hover:text-black");
            }
            const currentTab = document.getElementById(tabName);
            currentTab.style.display = "block";
            currentTab.classList.remove('hidden');
            currentTab.classList.add('block');
            evt.currentTarget.classList.remove("text-gray-500", "hover:text-black");
            evt.currentTarget.classList.add("border-b-2", "border-black", "text-black", "bg-white");
        }

        // 4. Favorite Logic
        function showFavoriteToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-6 right-6 z-[9999] px-4 py-3 rounded-lg text-sm font-semibold shadow-lg transition-all duration-300 ${type === 'success' ? 'bg-black text-white' : 'bg-blue-500 text-white'}`;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(() => toast.remove(), 300);
            }, 1800);
        }

        function toggleFavorite(productId) {
            const btn = document.getElementById('fav-btn');
            const icon = btn.querySelector('i');

            // Backend Fetch
            fetch('{{ route('favorit.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 401) {
                    alert('Silakan login terlebih dahulu!');
                    window.location.href = '{{ route('login') }}';
                    return;
                }

                if (data.isFavorited) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-blue-500');
                    btn.classList.add('text-blue-500');
                } else {
                    icon.classList.remove('fas', 'text-blue-500');
                    icon.classList.add('far');
                    btn.classList.remove('text-blue-500');
                }

                if (data.status === 'added') {
                    showFavoriteToast('Produk berhasil ditambahkan ke favorit.');
                }
                if (data.status === 'removed') {
                    showFavoriteToast('Produk dihapus dari favorit.');
                }
            });
        }

        // Check favorite status on load
        document.addEventListener('DOMContentLoaded', () => {
            @auth
                fetch('{{ route('favorit.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        check_only: true
                    })
                }).then(res => res.json()).then(data => {
                    if (data.isFavorited) {
                        const btn = document.getElementById('fav-btn');
                        const icon = btn.querySelector('i');
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-blue-500');
                        btn.classList.add('text-blue-500');
                    }
                });
            @endauth
        });
    </script>
@endsection
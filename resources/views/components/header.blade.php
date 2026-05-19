@php
    use App\Models\Wishlist;
    use App\Models\Keranjang;
    $isLoggedIn = auth()->check();
    $wishlistOwnerColumn = Wishlist::ownerColumn();
    $wishlistOwnerId = $isLoggedIn ? Wishlist::resolveOwnerId(auth()->user()) : null;
    $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;

    $cartOwnerColumn = Keranjang::ownerColumn();
    $cartOwnerId = $isLoggedIn ? Keranjang::resolveOwnerId(auth()->user()) : null;
    $cartCount = $cartOwnerId ? Keranjang::where($cartOwnerColumn, $cartOwnerId)->distinct()->count('detail_produk_id') : 0;
@endphp

<header class="bg-white shadow">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo-movr.svg') }}" alt="MOVR" class="h-10">
            </a>

            {{-- Kategori --}}
            <nav class="relative" x-data="{open:false}">
                <ul class="flex items-center gap-4">
                    @foreach($menuKategori ?? [] as $kategori)
                        <li class="group relative">
                            <a href="#" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-black">{{ $kategori->nama_kategori }}</a>

                            {{-- Mega dropdown --}}
                            <div class="absolute left-0 top-full mt-2 bg-white shadow-lg border rounded hidden group-hover:block z-50 w-[800px]">
                                <div class="p-6 grid grid-cols-4 gap-6">
                                    @foreach($kategori->children ?? [] as $sub)
                                        <div>
                                            <h4 class="font-semibold mb-2">{{ $sub->nama_kategori }}</h4>
                                            <ul class="text-sm text-gray-600 space-y-1">
                                                @foreach($sub->children ?? [] as $child)
                                                    <li>
                                                        <a href="/kategori/{{ $child->slug }}" class="hover:text-black">{{ $child->nama_kategori }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        {{-- Search + actions --}}
        <div class="flex items-center gap-4 flex-1 mx-6" x-data="searchComponent()">
        <div class="flex items-center gap-4 flex-1 mx-6" x-data="searchComponent()" x-init="init()">
            <form action="{{ route('product.search') }}" method="GET" class="flex-1">
                <div class="relative">
                    <input x-model="q" @input.debounce="onInput" name="q" type="text" placeholder="Cari produk, brand, kategori..." class="w-full border rounded px-4 py-2" autocomplete="off">

                    <div x-show="open && suggestions.length" x-cloak class="absolute z-50 left-0 right-0 bg-white border rounded mt-1 max-h-64 overflow-auto">
                        <template x-for="item in suggestions" :key="item.produk_id">
                            <a :href="'/produk/'+item.slug" class="block px-4 py-2 hover:bg-gray-50"> <span x-text="item.nama_produk"></span> </a>
                        </template>
                    </div>
                </div>
            </form>

            {{-- Wishlist --}}
            <div>
                <button @click="handleWishlist()" class="relative text-gray-600 hover:text-black">
                    <span class="text-lg">❤</span>
                    <span data-wishlist-badge x-text="counts.wishlist" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center" x-bind:class="{'hidden': counts.wishlist == 0}"></span>
                </button>
            </div>

            {{-- Cart --}}
            <div>
                <button @click="handleCart()" class="relative text-gray-600 hover:text-black">
                    <span class="text-lg">🛒</span>
                    <span data-cart-badge x-text="counts.cart" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center" x-bind:class="{'hidden': counts.cart == 0}"></span>
                </button>
            </div>

            {{-- Profile --}}
            <div class="relative" x-data="{open:false}">
                @if($isLoggedIn)
                    <button @click="open = !open" class="flex items-center gap-2">
                        <img src="{{ auth()->user()->foto_profil ? asset('storage/'.auth()->user()->foto_profil) : (auth()->user()->foto ?? asset('images/default-avatar.svg')) }}" alt="avatar" class="h-8 w-8 rounded-full object-cover" style="object-position: {{ auth()->user()->foto_profil_position ?? '50% 50%' }}">
                        <span class="text-sm">{{ auth()->user()->nama ?? auth()->user()->nama_lengkap ?? 'User' }}</span>
                    </button>

                    <div x-show="open" x-cloak class="absolute right-0 mt-2 bg-white border rounded shadow-lg w-48">
                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 hover:bg-gray-50">Profil Saya</a>
                        <a href="{{ route('order.index') }}" class="block px-4 py-2 hover:bg-gray-50">Pesanan Saya</a>
                        <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 hover:bg-gray-50">Wishlist</a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700">Login</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-700">Register</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function searchComponent(){
            return {
                q: '',
                open: false,
                suggestions: [],
                counts: { wishlist: {{ $wishlistCount }}, cart: {{ $cartCount }} },
                onInput(){
                    if(!this.q || this.q.length < 2){ this.open = false; this.suggestions = []; return; }
                    fetch('/api/search-suggest?q='+encodeURIComponent(this.q))
                        .then(r => r.json())
                        .then(data => { this.suggestions = data; this.open = data.length > 0; });
                },
                refreshCounts(){
                    fetch('/api/wishlist-count').then(r=>r.json()).then(j=>{ this.counts.wishlist = j.count || 0 });
                    fetch('/api/cart-count').then(r=>r.json()).then(j=>{ this.counts.cart = j.count || 0 });
                },
                handleWishlist(){
                    if(!{{ $isLoggedIn ? 'true' : 'false' }}){
                        window.location = '{{ route('login') }}?msg='+encodeURIComponent('Login untuk melihat wishlist kamu');
                        return;
                    }
                    window.location = '{{ route('wishlist.index') }}';
                },
                handleCart(){
                    if(!{{ $isLoggedIn ? 'true' : 'false' }}){
                        window.location = '{{ route('login') }}';
                        return;
                    }
                    window.location = '{{ route('cart.index') }}';
                },
                init(){
                    // listen for global cart-updated event
                    window.addEventListener('cart-updated', ()=>{ this.refreshCounts() });
                    window.addEventListener('wishlist-updated', ()=>{ this.refreshCounts() });
                }
            }
        }
    </script>
</header>

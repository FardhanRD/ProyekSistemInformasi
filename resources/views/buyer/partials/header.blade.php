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

    $user = auth()->user();
    $userName = $user->nama_pengguna ?? $user->nama ?? $user->name ?? 'User';
    $userEmail = $user->email ?? '';
    $userPhoto = $user->foto_profil ?? $user->foto ?? null;
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl" x-data="{ mobileOpen: false, profileOpen: false, searchQuery: '', wishlistCount: {{ $wishlistCount }}, cartCount: {{ $cartCount }} }">
    <div class="section-shell">
        <div class="flex h-20 items-center justify-between gap-4">
            <div class="flex items-center gap-4 lg:gap-6">
                <a href="{{ route('home') }}" class="flex h-12 w-44 items-center justify-center rounded-2xl bg-slate-950 px-4 text-lg font-black tracking-[0.2em] text-white shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-lg hover:shadow-slate-400/20">
                    MOVR
                </a>

                <nav class="hidden xl:flex items-center gap-1" x-cloak>
                    @foreach(($menuKategori ?? []) as $kategori)
                        <div class="group relative">
                            <a href="#" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-700 transition-all duration-200 hover:bg-[#63A2BB]/10 hover:text-[#63A2BB]">
                                {{ $kategori->nama_kategori }}
                            </a>

                            <div class="absolute left-0 top-full pt-3 opacity-0 invisible translate-y-2 transition-all duration-200 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0">
                                <div class="w-72 rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/60">
                                    @foreach($kategori->children ?? [] as $sub)
                                        <div class="mb-4 last:mb-0">
                                            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#63A2BB]">{{ $sub->nama_kategori }}</p>
                                            <div class="space-y-1">
                                                @foreach($sub->children ?? [] as $leaf)
                                                    <a href="{{ route('category.show', $leaf->slug) }}" class="block rounded-2xl px-3 py-2 text-sm text-slate-600 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">
                                                        {{ $leaf->nama_kategori }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>

            <div class="hidden flex-1 max-w-2xl xl:flex">
                <form action="{{ route('product.search') }}" method="GET" class="relative w-full">
                    <input type="text" name="q" x-model="searchQuery" placeholder="Cari produk, brand, kategori..." class="w-full rounded-full border border-slate-200 bg-[#F1F5F8] px-5 py-3 pr-12 text-sm text-slate-700 outline-none transition-all duration-200 focus:border-[#63A2BB] focus:bg-white focus:ring-4 focus:ring-[#63A2BB]/15">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-[#63A2BB] p-2 text-white transition-all duration-200 hover:bg-[#4A8BA3] hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.9-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB] xl:hidden" @click="mobileOpen = !mobileOpen">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('wishlist.index') }}" class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 10-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span x-cloak x-show="wishlistCount > 0" x-text="wishlistCount" class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#EF4444] px-1.5 text-[10px] font-bold text-white shadow-md"></span>
                </a>

                <a href="{{ route('cart.index') }}" class="relative inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H6.4M7 13L6.4 5M7 13l-1.5 3.5A1 1 0 007 18h10m-10 0a2 2 0 104 0m6 0a2 2 0 104 0" />
                    </svg>
                    <span x-cloak x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#63A2BB] px-1.5 text-[10px] font-bold text-white shadow-md"></span>
                </a>

                @auth
                    <div class="relative">
                        <button type="button" class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 pr-4 transition-all duration-200 hover:scale-105 hover:border-[#63A2BB]" @click="profileOpen = !profileOpen">
                            @if($userPhoto)
                                <img src="{{ str_starts_with($userPhoto, 'http') ? $userPhoto : asset('storage/' . $userPhoto) }}" alt="avatar" class="h-10 w-10 rounded-full object-cover ring-2 ring-[#63A2BB]/20">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#63A2BB] text-sm font-bold text-white ring-2 ring-[#63A2BB]/20">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                            @endif
                            <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ $userName }}</span>
                        </button>

                        <div x-cloak x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 top-full mt-3 w-64 rounded-3xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/60">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-bold text-slate-900">{{ $userName }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $userEmail }}</p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="mt-2 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">Profil Saya</a>
                            <a href="{{ route('order.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">Pesanan Saya</a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">Wishlist</a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-2">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-medium text-[#EF4444] transition-all duration-200 hover:bg-red-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('login') }}" class="rounded-full px-5 py-3 text-sm font-semibold text-slate-700 transition-all duration-200 hover:bg-[#63A2BB]/10 hover:text-[#63A2BB]">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>

        <div class="pb-4 xl:hidden" x-cloak x-show="mobileOpen">
            <div class="grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <form action="{{ route('product.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Cari produk..." class="w-full rounded-full border border-slate-200 bg-[#F1F5F8] px-4 py-3 text-sm outline-none focus:border-[#63A2BB] focus:ring-4 focus:ring-[#63A2BB]/15">
                </form>
                <div class="grid gap-2">
                    @foreach(($menuKategori ?? []) as $kategori)
                        <a href="#" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-[#63A2BB]/5 hover:text-[#63A2BB]">{{ $kategori->nama_kategori }}</a>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('wishlist.index') }}" class="btn-outline">Wishlist</a>
                    <a href="{{ route('cart.index') }}" class="btn-outline">Keranjang</a>
                </div>
                @guest
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>
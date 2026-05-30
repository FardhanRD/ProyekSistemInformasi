<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --admin-bg: #F8F9FA;
            --admin-brand: #2B9BAF;
            --admin-danger: #DC3545;
        }

        .sidebar-item-active { background: var(--admin-brand); color: #ffffff; }
        .sidebar-item { transition: background-color .15s ease, color .15s ease; }
    </style>
</head>
<body class="bg-[var(--admin-bg)] text-slate-900 font-sans">
    <div class="min-h-screen flex flex-col">
        {{-- TOPBAR --}}
        <header class="bg-white border-b border-slate-200">
            <div class="mx-auto max-w-7xl px-4 py-3">
                <div class="flex items-center gap-3 justify-between">
                                <div class="flex-1 flex items-center gap-3">
                        <form action="{{ url('/admin/search') }}" method="GET" class="w-full">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="cari produk, order, atau analytics"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--admin-brand)]/25"
                            />
                        </form>
                    </div>

                    <div class="flex items-center gap-3">
                                                <div class="relative" x-data="{
                                                    open: false,
                                                    notifs: [],
                                                    count: 0,
                                                    async load() {
                                                        try {
                                                            const res = await fetch('/admin/notifications/unread',
                                                                { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                                            const data = await res.json();
                                                            this.notifs = data.notifs;
                                                            this.count  = data.count;
                                                        } catch(e) {}
                                                    },
                                                    async markRead(id, url) {
                                                        await fetch('/admin/notifications/' + id + '/read', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        });
                                                        this.notifs = this.notifs.filter(n => n.id !== id);
                                                        this.count = Math.max(0, this.count - 1);
                                                        if (url) window.location.href = url;
                                                    },
                                                    async markAllRead() {
                                                        await fetch('/admin/notifications/read-all', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                'X-Requested-With': 'XMLHttpRequest'
                                                            }
                                                        });
                                                        this.count = 0;
                                                        this.notifs = [];
                                                    }
                                                }" x-init="load(); setInterval(() => load(), 20000)">

                                                    <button @click="open = !open"
                                                                    @click.outside="open = false"
                                                                    class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                                                        <svg class="w-5 h-5 text-gray-500" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                        </svg>
                                                        <span x-show="count > 0" x-cloak
                                                                    x-text="count > 9 ? '9+' : count"
                                                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold min-w-[16px] h-4 px-0.5 rounded-full flex items-center justify-center">
                                                        </span>
                                                    </button>

                                                    <div x-show="open" x-cloak
                                                             x-transition:enter="transition ease-out duration-150"
                                                             x-transition:enter-start="opacity-0 scale-95"
                                                             x-transition:enter-end="opacity-100 scale-100"
                                                             class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

                                                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                                            <span class="font-bold text-gray-800 text-sm">
                                                                Notifikasi
                                                            </span>
                                                            <button x-show="count > 0" @click="markAllRead()"
                                                                            class="text-xs text-[#63A2BB] hover:underline">
                                                                Tandai semua dibaca
                                                            </button>
                                                        </div>

                                                        <div class="max-h-80 overflow-y-auto">
                                                            <template x-if="notifs.length === 0">
                                                                <div class="px-4 py-8 text-center">
                                                                    <p class="text-xs text-gray-400">
                                                                        Tidak ada notifikasi baru
                                                                    </p>
                                                                </div>
                                                            </template>
                                                            <template x-for="n in notifs" :key="n.id">
                                                                <div @click="markRead(n.id, n.url)"
                                                                         class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 cursor-pointer transition flex gap-3">
                                                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center"
                                                                             :class="{
                                                                                 'bg-[#63A2BB]/10': n.jenis === 'transaksi',
                                                                                 'bg-green-50': n.jenis === 'pengiriman',
                                                                                 'bg-amber-50': n.jenis === 'promo',
                                                                                 'bg-red-50': n.jenis === 'stok',
                                                                                 'bg-gray-100': n.jenis === 'sistem',
                                                                             }">
                                                                        <span x-text="{
                                                                            transaksi: '🛒',
                                                                            pengiriman: '📦',
                                                                            promo: '🎁',
                                                                            stok: '⚠️',
                                                                            sistem: '⚙️'
                                                                        }[n.jenis] ?? '🔔'">
                                                                        </span>
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-sm font-semibold text-gray-800 line-clamp-1" x-text="n.judul"></p>
                                                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.pesan"></p>
                                                                        <p class="text-[11px] text-gray-400 mt-1" x-text="n.waktu"></p>
                                                                    </div>
                                                                    <div class="w-2 h-2 bg-[#63A2BB] rounded-full mt-2 flex-shrink-0"></div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                                            <span class="text-xs text-gray-400 font-medium">
                                                                Notifikasi terbaru admin
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>


                        {{-- Avatar + Nama --}}
                        <div class="relative group">
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                <div class="h-8 w-8 rounded-full bg-[var(--admin-brand)] text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr((auth()->user()->nama ?? 'A'),0,1)) }}
                                </div>
                                <span class="hidden sm:inline">Admin Panel / Master Administrator</span>
                                <span class="text-slate-500">▼</span>
                            </button>

                            <div class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white shadow-sm hidden group-hover:block">
                                <a href="{{ url('/profile') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Profil</a>
                                <a href="{{ url('/profile') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Pengaturan</a>
                                <div class="border-t border-slate-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 text-left text-sm text-red-600 hover:bg-slate-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- BODY --}}
        <div class="flex flex-1">
            {{-- SIDEBAR --}}
            <aside class="w-80 bg-white border-r border-slate-200 shadow-sm">
                <div class="p-5">
                    <div class="flex flex-col items-start gap-2">
                        <div class="text-2xl font-black text-[var(--admin-brand)]">MOVR</div>
                        <div class="text-sm font-semibold tracking-[0.2em] text-slate-500">DASHBOARD</div>
                    </div>
                </div>

                <nav class="px-5 pb-5">
                    {{-- Grup: DASHBOARD --}}
                    <div class="mt-2">
                        <p class="text-xs font-bold uppercase text-slate-400">MOVR</p>
                        <a href="{{ url('/admin/dashboard') }}" class="sidebar-item mt-2 flex items-center justify-between rounded-xl px-3 py-2 text-sm {{ request()->is('admin/dashboard') ? 'sidebar-item-active' : 'text-slate-700' }}">
                            <span class="font-semibold">DASHBOARD</span>
                            <span class="text-slate-400">▸</span>
                        </a>
                    </div>

                    {{-- Grup: MASTER DATA --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">MASTER DATA</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.master-product.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/master-product*') ? 'sidebar-item-active' : 'text-slate-700' }}">Master Prod</a>
                            <a href="{{ route('admin.category.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/category*') ? 'sidebar-item-active' : 'text-slate-700' }}">Category</a>
                            <a href="{{ route('admin.supplier.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier</a>
                        </div>
                    </div>

                    {{-- Grup: PRODUCT --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">PRODUCT</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.variant.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/variant*') ? 'sidebar-item-active' : 'text-slate-700' }}">Variant</a>
                            <a href="{{ route('admin.media.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/media*') ? 'sidebar-item-active' : 'text-slate-700' }}">Media</a>
                            <a href="{{ route('admin.pricing.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/pricing*') ? 'sidebar-item-active' : 'text-slate-700' }}">Pricing</a>
                        </div>
                    </div>

                    {{-- Grup: INVENTORY --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">INVENTORY</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.supplier-product.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier-product*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier Pr</a>
                            <a href="{{ route('admin.stock.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/stock') || request()->is('admin/stock/*') ? 'sidebar-item-active' : 'text-slate-700' }}">Stock</a>
                            <a href="{{ route('admin.stock-movement.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/stock-movement*') ? 'sidebar-item-active' : 'text-slate-700' }}">Stock Move</a>
                        </div>
                    </div>

                    {{-- Grup: TRANSACTION --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">TRANSACTION</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.supplier-order.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/supplier-order*') ? 'sidebar-item-active' : 'text-slate-700' }}">Supplier Or</a>
                            <a href="{{ route('admin.customer-order.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/customer-order*') ? 'sidebar-item-active' : 'text-slate-700' }}">Customer Or</a>
                        </div>
                    </div>

                    {{-- Grup: OTHER --}}
                    <div class="mt-5">
                        <p class="text-xs font-bold uppercase text-slate-400">OTHER</p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.review.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/review*') ? 'sidebar-item-active' : 'text-slate-700' }}">Review</a>
                            <a href="{{ route('admin.customer.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/customer') || request()->is('admin/customer/*') ? 'sidebar-item-active' : 'text-slate-700' }}">Customer</a>
                            <a href="{{ route('admin.promotion.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/promotion*') ? 'sidebar-item-active' : 'text-slate-700' }}">Promotion</a>
                            <a href="{{ route('admin.shipping.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/shipping*') ? 'sidebar-item-active' : 'text-slate-700' }}">Shipping</a>
                            <a href="{{ route('admin.report.index') }}" class="sidebar-item block rounded-xl px-3 py-2 text-sm {{ request()->is('admin/report*') ? 'sidebar-item-active' : 'text-slate-700' }}">Report</a>
                        </div>
                    </div>
                </nav>

                <div class="px-5 pb-6 mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-red-50 text-red-600 border border-red-100 px-4 py-3 text-sm font-semibold hover:bg-red-100">Sign Out</button>
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function showAdminToast(msg, type = 'success') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-amber-500'
            };

            const t = document.createElement('div');
            t.className = `fixed bottom-6 right-6 z-[9999] ${colors[type] || colors.success} text-white px-5 py-3 rounded-2xl shadow-xl text-sm font-medium flex items-center gap-2 transform translate-y-4 opacity-0 transition-all duration-300`;
            t.innerHTML = msg;
            document.body.appendChild(t);

            setTimeout(() => {
                t.style.opacity = '1';
                t.style.transform = 'translateY(0)';
            }, 10);

            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateY(10px)';
                setTimeout(() => t.remove(), 300);
            }, 3500);
        }
    </script>
</body>
</html>


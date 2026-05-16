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
                        {{-- Notifikasi real (5 terbaru) --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="relative inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 1 5.454 1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.642 23.848 23.848 0 0 1 5.454-1.31m5.715 0a24.255 24.255 0 0 0-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>

                                @if(isset($notifikasi) && $notifikasi->count() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                                    {{ $notifikasi->count() }}
                                </span>
                                @endif
                            </button>

                            <div x-show="open"
                                 @click.outside="open = false"
                                 @keydown.escape.window="open = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                                <div class="px-4 py-3 border-b flex justify-between items-center">
                                    <span class="font-semibold text-gray-700">Notifikasi</span>
                                    <span class="text-xs text-gray-400">{{ isset($notifikasi) ? $notifikasi->count() : 0 }} baru</span>
                                </div>

                                <div class="max-h-72 overflow-y-auto">
                                    @if(isset($notifikasi) && $notifikasi->count() > 0)
                                        @foreach($notifikasi as $notif)
                                            <a href="{{ $notif->url_redirect ?? '#' }}"
                                               class="block px-4 py-3 hover:bg-gray-50 border-b last:border-0 transition">
                                                <p class="font-medium text-sm text-gray-800">
                                                    {{ $notif->judul }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($notif->pesan, 60) }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="px-4 py-6 text-center text-gray-400 text-sm">
                                            Tidak ada notifikasi baru
                                        </div>
                                    @endif
                                </div>

                                <div class="px-4 py-2 border-t text-center">
                                    <a href="/admin/notification"
                                       class="text-xs text-teal-600 hover:underline">
                                        Lihat semua notifikasi
                                    </a>
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
</body>
</html>


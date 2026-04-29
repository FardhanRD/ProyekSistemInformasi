<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MOVR') }} Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        panel: {
                            bg: '#f2f4f8',
                            border: '#b8beca',
                            line: '#e7e9ef',
                            text: '#243348',
                            muted: '#8f97a6',
                            red: '#63a2bb',
                            redSoft: '#e8f3f7',
                        }
                    },
                    boxShadow: {
                        panel: '0 30px 80px rgba(57, 66, 95, 0.15)',
                    },
                    backgroundImage: {
                        dots: 'radial-gradient(circle, rgba(148, 161, 188, 0.34) 1px, transparent 1.4px)',
                    },
                }
            }
        };
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-panel-bg bg-dots bg-[length:18px_18px] font-sans text-panel-text">
    @php
        $adminName = auth()->user()->name ?? 'Master Administrator';
        $avatarLetter = strtoupper(substr($adminName, 0, 1));
        $sidebarMenus = [
            'MASTER DATA' => [
                ['label' => 'Produk Master', 'icon' => 'fa-database', 'route' => route('admin.produk.index'), 'active' => request()->routeIs('admin.produk.*')],
                ['label' => 'Kategori', 'icon' => 'fa-shapes', 'route' => route('admin.kategori.index'), 'active' => request()->routeIs('admin.kategori.*')],
                ['label' => 'Supplier', 'icon' => 'fa-box-open', 'route' => route('admin.suppliers.index'), 'active' => request()->routeIs('admin.suppliers.*')],
            ],
            'PRODUK' => [
                ['label' => 'Varian', 'icon' => 'fa-gem', 'route' => route('admin.master-data.products.index'), 'active' => request()->routeIs('admin.master-data.variants.*')],
                ['label' => 'Media', 'icon' => 'fa-image', 'route' => route('admin.produk.index'), 'active' => false],
                ['label' => 'Pricing', 'icon' => 'fa-tags', 'route' => route('admin.master-data.products.index'), 'active' => request()->routeIs('admin.master-data.pricing.*')],
            ],
            'INVENTORI' => [
                ['label' => 'Produk Supplier', 'icon' => 'fa-truck-field', 'route' => route('admin.inventory.dashboard'), 'active' => request()->routeIs('admin.inventory.supplier-products.*')],
                ['label' => 'Stok', 'icon' => 'fa-boxes-stacked', 'route' => route('admin.inventory.dashboard'), 'active' => request()->routeIs('admin.inventory.dashboard')],
                ['label' => 'Stok Movement', 'icon' => 'fa-arrow-right-arrow-left', 'route' => route('admin.inventory.movements.index'), 'active' => request()->routeIs('admin.inventory.movements.*')],
            ],
            'TRANSAKSI' => [
                ['label' => 'Order Supplier', 'icon' => 'fa-cart-plus', 'route' => route('admin.purchases.index'), 'active' => request()->routeIs('admin.purchases.*')],
                ['label' => 'Order Customer', 'icon' => 'fa-cart-shopping', 'route' => route('admin.orders.index'), 'active' => request()->routeIs('admin.orders.*') || request()->routeIs('admin.order-management.*')],
            ],
            'LAINNYA' => [
                ['label' => 'Review', 'icon' => 'fa-star', 'route' => route('admin.reviews.index'), 'active' => request()->routeIs('admin.reviews.*')],
                ['label' => 'Customer', 'icon' => 'fa-user-group', 'route' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
                ['label' => 'Promo', 'icon' => 'fa-ticket', 'route' => route('admin.promo.vouchers.index'), 'active' => request()->routeIs('admin.promo.*')],
                ['label' => 'Pengiriman', 'icon' => 'fa-truck-fast', 'route' => route('admin.logistics.shipping-settings.index'), 'active' => request()->routeIs('admin.logistics.*')],
                ['label' => 'Laporan', 'icon' => 'fa-chart-column', 'route' => route('admin.report'), 'active' => request()->routeIs('admin.report')],
            ],
        ];
    @endphp

    <div class="w-full min-h-screen bg-panel-bg">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-[280px_1fr]">
            <aside class="hidden border-r border-panel-line bg-white lg:flex lg:flex-col">
                <div class="border-b border-panel-line px-6 py-6">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-md bg-panel-red text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/>
                                <path d="M8 10h8M8 14h5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[1.15rem] font-extrabold leading-tight text-[#3f7f97]">Shop Manager</p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-panel-muted">Premium Reliability</p>
                        </div>
                    </a>
                </div>

                <nav class="flex-1 space-y-4 overflow-y-auto px-5 py-5 text-[0.95rem]">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg border-l-[3px] px-3 py-2.5 font-bold {{ request()->routeIs('admin.dashboard') ? 'border-panel-red bg-panel-redSoft text-panel-red' : 'border-transparent text-panel-text hover:bg-[#f6f7fb]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>
                        </svg>
                        Dashboard
                    </a>

                    @foreach($sidebarMenus as $section => $items)
                        <div>
                            <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-[#a5aebe]">{{ $section }}</p>
                            <div class="space-y-0.5">
                                @foreach($items as $item)
                                    <a href="{{ $item['route'] }}" class="flex items-center gap-2.5 rounded-lg border-l-[3px] px-3 py-2 text-[14px] font-semibold {{ $item['active'] ? 'border-panel-red bg-panel-redSoft text-panel-red' : 'border-transparent text-[#4a5568] hover:bg-[#f7f8fc]' }}">
                                        <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-[12px] {{ $item['active'] ? 'text-panel-red' : 'text-[#7c8799]' }}"></i>
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                <div class="border-t border-panel-line p-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-panel-red px-3 py-2.5 text-sm font-bold text-white transition hover:brightness-95">Logout</button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-[84vh] flex-col">
                <header class="border-b border-panel-line bg-white/90 px-3 py-3 backdrop-blur sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-[230px] flex-1">
                            <label class="flex items-center gap-2 rounded-full border border-panel-line bg-[#f7f8fb] px-4 py-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#9aa3b4]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m21 21-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                                <input type="text" placeholder="Search analytics, products, or orders..." class="w-full bg-transparent text-sm text-panel-text placeholder:text-[#a2aabb] focus:outline-none">
                            </label>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-5">
                            <button type="button" class="rounded-full p-1.5 text-[#7f8898] hover:bg-[#f3f5fa]" aria-label="Notifications">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                                    <path d="M9 17a3 3 0 0 0 6 0"/>
                                </svg>
                            </button>

                            <button type="button" class="rounded-full p-1.5 text-[#7f8898] hover:bg-[#f3f5fa]" aria-label="Help">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 2-3 4"/>
                                    <path d="M12 17h.01"/>
                                </svg>
                            </button>

                            <div class="hidden h-9 w-px bg-panel-line sm:block"></div>

                            <div class="flex items-center gap-2.5">
                                <div class="text-right leading-tight">
                                    <p class="text-sm font-extrabold text-panel-text">Admin Panel</p>
                                    <p class="text-[11px] font-semibold text-panel-muted">Master Administrator</p>
                                </div>
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#1f2837] text-sm font-bold text-white">
                                    {{ $avatarLetter }}
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden px-3 py-4 sm:px-6 sm:py-5">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>
</html>

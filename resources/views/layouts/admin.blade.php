<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — MOVR</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }

        * { font-family: 'Inter', sans-serif; }

        :root {
            --admin-bg: #F0F4F8;
            --admin-brand: #63A2BB;
            --admin-brand-dark: #4A8BA3;
            --admin-sidebar: #0F172A;
            --admin-sidebar-hover: #1E293B;
            --admin-danger: #EF4444;
        }

        /* Sidebar */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #94A3B8;
            transition: all 0.15s ease;
            text-decoration: none;
            margin-bottom: 2px;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.07);
            color: #E2E8F0;
        }
        .sidebar-link.active {
            background: var(--admin-brand);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99,162,187,0.35);
        }
        .sidebar-link .icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.75;
        }
        .sidebar-link.active .icon {
            opacity: 1;
        }
        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #475569;
            padding: 0 14px;
            margin: 20px 0 6px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Page content area */
        .admin-content {
            flex: 1;
            overflow-y: auto;
            background: var(--admin-bg);
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
            transform: translateY(-1px);
        }

        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            background: #F8FAFC;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94A3B8;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #F1F5F9;
        }
        .admin-table td {
            padding: 13px 16px;
            border-bottom: 1px solid #F8FAFC;
            font-size: 13.5px;
            color: #334155;
            vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover td { background: #FAFCFE; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-success { background: #DCFCE7; color: #16A34A; }
        .badge-warning { background: #FEF3C7; color: #D97706; }
        .badge-danger  { background: #FEE2E2; color: #DC2626; }
        .badge-info    { background: #EFF6FF; color: #2563EB; }
        .badge-admin   { background: rgba(99,162,187,0.12); color: var(--admin-brand); }

        /* Btn */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            background: var(--admin-brand);
            color: white;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(99,162,187,0.3);
        }
        .btn-primary:hover {
            background: var(--admin-brand-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(99,162,187,0.4);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: white;
            color: #475569;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #E2E8F0;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-secondary:hover {
            border-color: var(--admin-brand);
            color: var(--admin-brand);
            background: rgba(99,162,187,0.04);
        }
        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #FEF2F2;
            color: #EF4444;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: 1.5px solid #FEE2E2;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-danger:hover { background: #FEE2E2; border-color: #FECACA; }

        /* Input */
        .form-input {
            width: 100%;
            padding: 9px 14px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            font-size: 13.5px;
            color: #1E293B;
            background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .form-input:focus {
            border-color: var(--admin-brand);
            box-shadow: 0 0 0 3px rgba(99,162,187,0.1);
        }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #64748B;
            margin-bottom: 6px;
        }

        /* Page header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .page-header-title { font-size: 22px; font-weight: 800; color: #0F172A; }
        .page-header-sub { font-size: 13px; color: #94A3B8; margin-top: 3px; }

        /* Card panel */
        .panel {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: #1E293B;
        }
        .panel-body { padding: 20px; }

        /* Toast */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }

        /* Topbar */
        .topbar {
            height: 60px;
            background: white;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 30;
            box-shadow: 0 1px 0 #F1F5F9;
        }

        /* Sidebar dims */
        .admin-sidebar {
            width: 240px;
            flex-shrink: 0;
            background: var(--admin-sidebar);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
        }
    </style>
    @yield('head')
</head>
<body style="background: var(--admin-bg); color: #1E293B;">
<div style="display:flex; min-height:100vh;">

    {{-- SIDEBAR --}}
    <aside class="admin-sidebar">
        {{-- Logo --}}
        <div style="padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div style="display:flex; align-items:center; gap:12px;">
                <img src="{{ asset('images/logo-dashboard.png') }}" alt="MOVR Logo" style="width:46px; height:46px; object-fit: contain; flex-shrink:0;">
                <div>
                    <div style="font-weight:800; font-size:15px; color:white; letter-spacing:-0.3px;">MOVR</div>
                    <div style="font-size:10px; font-weight:600; color:#475569; letter-spacing:0.1em; text-transform:uppercase;">Admin Panel</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="padding: 12px 10px; flex:1; overflow-y:auto;">
            <p class="sidebar-section-label">Overview</p>
            <a href="{{ url('/admin/dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <p class="sidebar-section-label">Master Data</p>
            <a href="{{ route('admin.master-product.index') }}" class="sidebar-link {{ request()->routeIs('admin.master-product.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Master Produk
            </a>
            <a href="{{ route('admin.category.index') }}" class="sidebar-link {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>
            <a href="{{ route('admin.supplier.index') }}" class="sidebar-link {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Supplier
            </a>

            <p class="sidebar-section-label">Produk</p>
            <a href="{{ route('admin.variant.index') }}" class="sidebar-link {{ request()->routeIs('admin.variant.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                Varian
            </a>
            <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Media
            </a>

            <p class="sidebar-section-label">Inventori</p>
            <a href="{{ route('admin.supplier-product.index') }}" class="sidebar-link {{ request()->routeIs('admin.supplier-product.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Produk Supplier
            </a>
            <a href="{{ route('admin.stock.index') }}" class="sidebar-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Stok
            </a>
            <a href="{{ route('admin.stock-movement.index') }}" class="sidebar-link {{ request()->routeIs('admin.stock-movement.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Pergerakan Stok
            </a>

            <p class="sidebar-section-label">Transaksi</p>
            <a href="{{ route('admin.supplier-order.index') }}" class="sidebar-link {{ request()->routeIs('admin.supplier-order.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Order Supplier
            </a>
            <a href="{{ route('admin.customer-order.index') }}" class="sidebar-link {{ request()->routeIs('admin.customer-order.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Order Customer
            </a>

            <p class="sidebar-section-label">Lainnya</p>
            <a href="{{ route('admin.review.index') }}" class="sidebar-link {{ request()->routeIs('admin.review.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Review
            </a>
            <a href="{{ route('admin.customer.index') }}" class="sidebar-link {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Customer
            </a>
            <a href="{{ route('admin.promotion.index') }}" class="sidebar-link {{ request()->routeIs('admin.promotion.*') && request('tab') !== 'flash' ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Promosi
            </a>
            <a href="{{ route('admin.promotion.index') }}?tab=flash" class="sidebar-link {{ request()->routeIs('admin.promotion.*') && request('tab') === 'flash' ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Flash Sale
            </a>
            <a href="{{ route('admin.shipping.index') }}" class="sidebar-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Pengiriman
            </a>
            <a href="{{ route('admin.report.index') }}" class="sidebar-link {{ request()->routeIs('admin.report.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
        </nav>

        {{-- Footer --}}
        <div style="padding: 12px 10px; border-top: 1px solid rgba(255,255,255,0.06);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%; display:flex; align-items:center; gap:8px; padding:9px 14px; background:rgba(239,68,68,0.1); color:#F87171; border-radius:10px; font-size:13px; font-weight:600; border:none; cursor:pointer; transition: all 0.15s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <div style="flex:1; display:flex; flex-direction:column; min-width:0; overflow:hidden;">

        {{-- TOPBAR --}}
        <header class="topbar">
            {{-- Search --}}
            <form action="{{ url('/admin/search') }}" method="GET" style="flex:1; max-width:400px;">
                <div style="position:relative;">
                    <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk, order, customer..." style="width:100%; padding:8px 14px 8px 38px; border:1.5px solid #E2E8F0; border-radius:10px; font-size:13px; background:#F8FAFC; outline:none; color:#1E293B; transition:border-color 0.15s;" onfocus="this.style.borderColor='#63A2BB'; this.style.background='white';" onblur="this.style.borderColor='#E2E8F0'; this.style.background='#F8FAFC';">
                </div>
            </form>

            <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                {{-- Notification Bell --}}
                <div class="relative" x-data="{
                    open: false,
                    notifs: [],
                    count: 0,
                    async load() {
                        try {
                            const res = await fetch('/admin/notifications/unread', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const data = await res.json();
                            this.notifs = data.notifs;
                            this.count = data.count;
                        } catch(e) {}
                    },
                    async markRead(id, url) {
                        await fetch('/admin/notifications/' + id + '/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' } });
                        this.notifs = this.notifs.filter(n => n.id !== id);
                        this.count = Math.max(0, this.count - 1);
                        if (url) window.location.href = url;
                    },
                    async markAllRead() {
                        await fetch('/admin/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' } });
                        this.count = 0; this.notifs = [];
                    }
                }" x-init="load(); setInterval(() => load(), 20000)">
                    <button id="notificationBell" @click="open = !open" @click.outside="open = false" style="position:relative; width:36px; height:36px; border-radius:10px; background:#F8FAFC; border:1.5px solid #E2E8F0; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.borderColor='#63A2BB'" onmouseout="this.style.borderColor='#E2E8F0'">
                        <svg width="17" height="17" fill="none" stroke="#64748B" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <template x-if="count > 0">
                            <span x-cloak x-text="count > 9 ? '9+' : count" style="position:absolute; top:-4px; right:-4px; background:#EF4444; color:white; font-size:9px; font-weight:800; min-width:16px; height:16px; border-radius:99px; display:flex; align-items:center; justify-content:center; padding:0 3px; border:2px solid white;"></span>
                        </template>
                    </button>

                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" style="position:absolute; right:0; top:calc(100% + 8px); width:320px; background:white; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.12); border:1px solid #F1F5F9; z-index:100; overflow:hidden;">
                        <div style="padding:14px 16px; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-weight:700; font-size:13px; color:#1E293B;">Notifikasi</span>
                            <button x-show="count > 0" @click="markAllRead()" style="font-size:11px; color:#63A2BB; font-weight:600; background:none; border:none; cursor:pointer;">Tandai semua dibaca</button>
                        </div>
                        <div style="max-height:320px; overflow-y:auto;">
                            <template x-if="notifs.length === 0">
                                <div style="padding:32px 16px; text-align:center; color:#94A3B8; font-size:13px;">Tidak ada notifikasi baru</div>
                            </template>
                            <template x-for="n in notifs" :key="n.id">
                                <div @click="markRead(n.id, n.url)" style="padding:12px 16px; border-bottom:1px solid #F8FAFC; cursor:pointer; transition:background 0.1s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='white'">
                                    <p style="font-size:13px; font-weight:600; color:#1E293B; margin:0 0 3px;" x-text="n.judul"></p>
                                    <p style="font-size:12px; color:#64748B; margin:0;" x-text="n.pesan"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Avatar --}}
                <div class="relative group" style="position:relative;">
                    <button type="button" style="display:flex; align-items:center; gap:8px; padding:6px 12px 6px 6px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.borderColor='#63A2BB'" onmouseout="this.style.borderColor='#E2E8F0'">
                        <div style="width:28px; height:28px; background: linear-gradient(135deg, #63A2BB, #4A8BA3); border-radius:8px; color:white; font-weight:800; font-size:12px; display:flex; align-items:center; justify-content:center;">
                            {{ strtoupper(substr((auth()->user()->nama ?? 'A'),0,1)) }}
                        </div>
                        <span style="font-size:13px; font-weight:600; color:#334155; display:none;" class="sm:inline">Admin</span>
                        <svg width="12" height="12" fill="none" stroke="#94A3B8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="hidden group-hover:block" style="position:absolute; right:0; top:calc(100% + 8px); width:200px; background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.1); border:1px solid #F1F5F9; overflow:hidden; z-index:100;">
                        <a href="{{ url('/profile') }}" style="display:block; padding:11px 16px; font-size:13px; color:#334155; text-decoration:none; transition:background 0.1s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='white'">Profil Saya</a>
                        <a href="{{ url('/profile') }}" style="display:block; padding:11px 16px; font-size:13px; color:#334155; text-decoration:none; transition:background 0.1s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='white'">Pengaturan</a>
                        <div style="border-top:1px solid #F1F5F9;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%; text-align:left; padding:11px 16px; font-size:13px; color:#EF4444; background:none; border:none; cursor:pointer; transition:background 0.1s;" onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='white'">Sign Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="admin-content">
            @if(session('success'))
                <div class="alert-success" style="margin:16px 24px 0; padding:12px 16px; background:#DCFCE7; border:1px solid #BBF7D0; border-radius:12px; color:#16A34A; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-danger" style="margin:16px 24px 0; padding:12px 16px; background:#FEE2E2; border:1px solid #FECACA; border-radius:12px; color:#DC2626; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script>
    function showAdminToast(msg, type = 'success') {
        const palette = {
            success: { bg: '#DCFCE7', color: '#16A34A', border: '#BBF7D0', icon: '✓' },
            error:   { bg: '#FEE2E2', color: '#DC2626', border: '#FECACA', icon: '✕' },
            warning: { bg: '#FEF3C7', color: '#D97706', border: '#FDE68A', icon: '!' },
        };
        const p = palette[type] || palette.success;
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; bottom:24px; right:24px; z-index:9999; padding:12px 18px; background:${p.bg}; border:1px solid ${p.border}; border-radius:12px; color:${p.color}; font-size:13px; font-weight:700; display:flex; align-items:center; gap:8px; box-shadow:0 8px 24px rgba(0,0,0,0.1); transform:translateY(16px); opacity:0; transition:all 0.3s ease;`;
        t.innerHTML = `<span style="width:20px;height:20px;background:${p.color};color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;">${p.icon}</span>${msg}`;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '1'; t.style.transform = 'translateY(0)'; }, 10);
        setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; setTimeout(() => t.remove(), 300); }, 3500);
    }
</script>
@yield('scripts')
</body>
</html>

@extends('layouts.admin')

@section('title', 'Dashboard Analytics')

@section('content')
<div style="padding:28px 28px 40px;" x-data="{ activeFilter: 'overview' }">

    {{-- Page Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <p style="font-size:11px; font-weight:700; color:#63A2BB; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 4px;">Dashboard</p>
            <h1 style="font-size:22px; font-weight:800; color:#0F172A; margin:0 0 4px;">Analytics Overview</h1>
            <p style="font-size:13px; color:#94A3B8; margin:0;">Analitik transaksi, produk, dan kategori.</p>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('admin.report.export', ['start' => request('start', $start->toDateString()), 'end' => request('end', $end->toDateString())]) }}" class="btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </a>
            <a href="{{ route('admin.dashboard.import') }}" class="btn-secondary">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import
            </a>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div style="background:white; border:1px solid #E2E8F0; border-radius:14px; padding:14px 18px; margin-bottom:24px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        {{-- Mode dropdown --}}
        <div style="position:relative;" x-data="{ open:false }">
            <button @click="open=!open" @click.outside="open=false"
                style="display:flex; align-items:center; gap:8px; padding:8px 14px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; font-size:13px; font-weight:600; color:#334155; cursor:pointer; min-width:200px; justify-content:space-between; transition:border-color 0.15s;"
                :style="open ? 'border-color:#63A2BB;' : ''">
                <span x-text="{ overview: '📊 Overview', produk: '👟 Dashboard Produk', kategori: '🗂 Dashboard Kategori' }[activeFilter]"></span>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak style="position:absolute; top:calc(100% + 6px); left:0; width:220px; background:white; border:1px solid #E2E8F0; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.1); z-index:50; overflow:hidden;">
                <button @click="activeFilter='overview'; open=false" style="width:100%; text-align:left; padding:10px 14px; font-size:13px; border:none; cursor:pointer; transition:background 0.1s;" :style="activeFilter==='overview' ? 'background:rgba(99,162,187,0.08); color:#63A2BB; font-weight:700;' : 'background:white; color:#334155;'" onmouseover="if(this.dataset.a!=='1')this.style.background='#F8FAFC'" onmouseout="if(this.dataset.a!=='1')this.style.background='white'" :data-a="activeFilter==='overview' ? '1' : '0'">📊 Overview</button>
                <button @click="activeFilter='produk'; open=false" style="width:100%; text-align:left; padding:10px 14px; font-size:13px; border:none; cursor:pointer; transition:background 0.1s;" :style="activeFilter==='produk' ? 'background:rgba(99,162,187,0.08); color:#63A2BB; font-weight:700;' : 'background:white; color:#334155;'">👟 Dashboard Produk</button>
                <button @click="activeFilter='kategori'; open=false" style="width:100%; text-align:left; padding:10px 14px; font-size:13px; border:none; cursor:pointer; transition:background 0.1s;" :style="activeFilter==='kategori' ? 'background:rgba(99,162,187,0.08); color:#63A2BB; font-weight:700;' : 'background:white; color:#334155;'">🗂 Dashboard Kategori</button>
            </div>
        </div>

        <div style="width:1px; height:28px; background:#E2E8F0; flex-shrink:0;"></div>

        {{-- Date filter --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:6px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; padding:6px 12px;">
                <svg width="14" height="14" fill="none" stroke="#94A3B8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input type="date" name="start" value="{{ request('start', $start->toDateString()) }}" style="background:transparent; border:none; font-size:13px; color:#334155; outline:none; cursor:pointer;">
            </div>
            <span style="color:#CBD5E1; font-size:12px;">→</span>
            <div style="display:flex; align-items:center; gap:6px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; padding:6px 12px;">
                <svg width="14" height="14" fill="none" stroke="#94A3B8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input type="date" name="end" value="{{ request('end', $end->toDateString()) }}" style="background:transparent; border:none; font-size:13px; color:#334155; outline:none; cursor:pointer;">
            </div>
            <button type="submit" class="btn-primary" style="padding:8px 16px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('admin.dashboard') }}" style="font-size:13px; color:#94A3B8; text-decoration:none; padding:8px 4px;">Reset</a>
        </form>
    </div>

    {{-- ===== OVERVIEW TAB ===== --}}
    <div x-show="activeFilter === 'overview'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @php
            $revUp = ($revenueChange >= 0);
            $ordUp = ($ordersChange >= 0);
            $avgUp = ($avgChange >= 0);
        @endphp

        {{-- KPI cards --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
            @foreach([
                ['label'=>'Total Revenue','value'=>'Rp '.number_format($totalRevenue,0,',','.'),'change'=>number_format($revenueChange,2),'up'=>$revUp,'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','iconBg'=>'#EFF8FB','iconColor'=>'#63A2BB'],
                ['label'=>'Total Orders','value'=>$totalOrders,'change'=>number_format($ordersChange,2),'up'=>$ordUp,'icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z','iconBg'=>'#F0FDF4','iconColor'=>'#16A34A'],
                ['label'=>'Active Customers','value'=>$activeCustomers,'change'=>'—','up'=>true,'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','iconBg'=>'#FEF9EE','iconColor'=>'#D97706'],
                ['label'=>'Avg Order Value','value'=>'Rp '.number_format($avgOrderValue??0,0,',','.'),'change'=>number_format($avgChange,2),'up'=>$avgUp,'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','iconBg'=>'#FFF1F2','iconColor'=>'#E11D48'],
            ] as $kpi)
            <div class="stat-card" style="padding:20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:{{ $kpi['iconBg'] }}; display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="{{ $kpi['iconColor'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $kpi['icon'] }}"/></svg>
                    </div>
                    @if($kpi['change'] !== '—')
                    <span style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:99px; {{ $kpi['up'] ? 'background:#DCFCE7; color:#16A34A;' : 'background:#FEE2E2; color:#DC2626;' }}">
                        {{ $kpi['up'] ? '▲' : '▼' }} {{ $kpi['change'] }}%
                    </span>
                    @endif
                </div>
                <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 6px;">{{ $kpi['label'] }}</p>
                <p style="font-size:22px; font-weight:800; color:#0F172A; margin:0;">{{ $kpi['value'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Charts row --}}
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px;">
            <div class="panel">
                <div class="panel-header">
                    <p class="panel-title">Performance Trends</p>
                    <span style="font-size:11px; color:#94A3B8;">Revenue: Aktif vs Previous</span>
                </div>
                <div class="panel-body"><canvas id="trendChart" height="130"></canvas></div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <p class="panel-title">Sales Distribution</p>
                    <span style="font-size:11px; color:#94A3B8;">By Category</span>
                </div>
                <div class="panel-body">
                    <canvas id="donutChart" height="160" style="max-width:160px; margin:0 auto 16px;"></canvas>
                    <ul style="list-style:none; padding:0; margin:0; space-y:6px;">
                        @foreach($categoryRevenue as $i)
                        <li style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <span style="width:8px; height:8px; border-radius:50%; background:#63A2BB; flex-shrink:0;"></span>
                            <span style="font-size:12px; color:#64748B; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $i->category }}</span>
                            <span style="font-size:12px; font-weight:700; color:#1E293B;">Rp {{ number_format($i->revenue,0,',','.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="panel" style="margin-bottom:16px;">
            <div class="panel-header">
                <p class="panel-title">Monthly Revenue Comparison</p>
                <span style="font-size:11px; color:#94A3B8;">This Year vs Last Year</span>
            </div>
            <div class="panel-body"><canvas id="monthlyBarChart" height="110"></canvas></div>
        </div>

        {{-- Rating + Reviews --}}
        <div style="display:grid; grid-template-columns:1fr 2fr; gap:16px; margin-bottom:16px;">
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Rating Toko</p></div>
                <div class="panel-body">
                    <div style="display:flex; align-items:flex-end; gap:10px; margin-bottom:8px;">
                        <span style="font-size:42px; font-weight:900; color:#0F172A; line-height:1;">{{ number_format($storeRatingAverage??0,1) }}</span>
                        <span style="font-size:14px; color:#94A3B8; padding-bottom:6px;">/ 5.0</span>
                    </div>
                    <div style="color:#F59E0B; font-size:20px; letter-spacing:2px; margin-bottom:8px;">
                        @for($i=1;$i<=5;$i++){{ $i <= round($storeRatingAverage??0) ? '★' : '☆' }}@endfor
                    </div>
                    <p style="font-size:12px; color:#94A3B8; margin-bottom:16px;">{{ $storeRatingCount??0 }} ulasan diterima</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        <div style="background:#F8FAFC; border-radius:10px; padding:10px;">
                            <p style="font-size:10px; font-weight:700; color:#94A3B8; text-transform:uppercase; margin:0 0 4px;">Pelayanan</p>
                            <p style="font-size:20px; font-weight:800; color:#0F172A; margin:0;">{{ number_format($storeServiceAverage??0,1) }}</p>
                        </div>
                        <div style="background:#F8FAFC; border-radius:10px; padding:10px;">
                            <p style="font-size:10px; font-weight:700; color:#94A3B8; text-transform:uppercase; margin:0 0 4px;">Aplikasi</p>
                            <p style="font-size:20px; font-weight:800; color:#0F172A; margin:0;">{{ number_format($storeAppAverage??0,1) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <p class="panel-title">Ulasan Toko Terbaru</p>
                    <span style="font-size:11px; color:#94A3B8;">Evaluasi operasional</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="admin-table">
                        <thead><tr>
                            <th>Toko</th><th>Buyer</th><th>Pelayanan</th><th>Aplikasi</th><th>Rating</th><th>Komentar</th><th>Tanggal</th>
                        </tr></thead>
                        <tbody>
                            @forelse($storeRatingLatest as $rating)
                                @php
                                    $buyerName=$rating->buyer?->pengguna?->nama_pengguna??'Pembeli';
                                    $maskedBuyerName=mb_strlen($buyerName)>2?mb_substr($buyerName,0,2).str_repeat('*',max(mb_strlen($buyerName)-2,3)):$buyerName;
                                @endphp
                                <tr>
                                    <td style="font-weight:600;">{{ $rating->supplier?->nama_toko??'-' }}</td>
                                    <td>{{ $maskedBuyerName }}</td>
                                    <td>{{ number_format((float)($rating->pelayanan??$rating->bintang??0),1) }}</td>
                                    <td>{{ number_format((float)($rating->aplikasi??$rating->bintang??0),1) }}</td>
                                    <td><span style="color:#F59E0B; font-weight:700;">{{ number_format((float)$rating->bintang,1) }} ★</span></td>
                                    <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ \Illuminate\Support\Str::limit($rating->komentar??'-',60) }}</td>
                                    <td style="color:#94A3B8; font-size:12px;">{{ \Carbon\Carbon::parse($rating->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="text-align:center; color:#94A3B8; padding:24px;">Belum ada rating toko pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="panel">
            <div class="panel-header">
                <p class="panel-title">Recent Activities</p>
                <span style="font-size:11px; color:#94A3B8;">Last 10 actions</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead><tr><th>Timestamp</th><th>Deskripsi</th><th>Admin</th></tr></thead>
                    <tbody>
                        @forelse($recentActivities as $log)
                            <tr>
                                <td style="color:#94A3B8; font-size:12px; white-space:nowrap;">{{ $log->timestamp??'-' }}</td>
                                <td>{{ $log->description??'-' }}</td>
                                <td><span class="badge badge-admin">{{ $log->admin_name??($log->admin??'-') }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center; color:#94A3B8; padding:24px;">Belum ada data admin_log.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== PRODUK TAB ===== --}}
    <div x-show="activeFilter === 'produk'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @php
            $totalProduk = \App\Models\Produk::count();
            $produkAktif = \App\Models\Produk::where('is_active', 1)->count();
            $produkHabis = \App\Models\DetailProduk::where('stok', 0)->count();
            $produkBaru  = \App\Models\Produk::where('penyimpanan_waktu', '>=', now()->subDays(7))->count();
            $perSupplier = \App\Models\Produk::with('supplier')->select('supplier_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))->groupBy('supplier_id')->orderByDesc('total')->limit(5)->get();
            $topProduk   = \App\Models\Produk::with('gambarUtama')->where('is_active',1)->orderByDesc('total_terjual')->limit(5)->get();
        @endphp

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px;">
            @foreach([
                ['label'=>'Total Produk','val'=>$totalProduk,'color'=>'#63A2BB','bg'=>'#EFF8FB'],
                ['label'=>'Produk Aktif','val'=>$produkAktif,'color'=>'#16A34A','bg'=>'#F0FDF4'],
                ['label'=>'Stok Habis','val'=>$produkHabis,'color'=>'#DC2626','bg'=>'#FEF2F2'],
                ['label'=>'Baru (7 hari)','val'=>$produkBaru,'color'=>'#D97706','bg'=>'#FFFBEB'],
            ] as $st)
            <div class="stat-card" style="padding:18px;">
                <p style="font-size:28px; font-weight:900; color:{{ $st['color'] }}; margin:0 0 6px;">{{ $st['val'] }}</p>
                <p style="font-size:11px; font-weight:600; color:#94A3B8; margin:0;">{{ $st['label'] }}</p>
            </div>
            @endforeach
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Produk per Supplier</p></div>
                <div class="panel-body" style="space-y:8px;">
                    @foreach($perSupplier as $item)
                        @php $pct = $totalProduk > 0 ? round($item->total/$totalProduk*100) : 0; @endphp
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                            <span style="font-size:12px; color:#475569; width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex-shrink:0;">{{ $item->supplier?->nama_toko??'-' }}</span>
                            <div style="flex:1; height:8px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                                <div style="height:8px; background:#63A2BB; border-radius:99px; width:{{ $pct }}%; transition:width 0.6s;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:#334155; width:20px; text-align:right;">{{ $item->total }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Top Produk Terlaris</p></div>
                <div class="panel-body">
                    @foreach($topProduk as $p)
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <img src="{{ $p->gambarUtama?->url_safe??asset('images/placeholder.png') }}" style="width:36px; height:36px; border-radius:8px; object-fit:cover; background:#F1F5F9; flex-shrink:0;" alt="{{ $p->nama_produk }}">
                            <span style="flex:1; font-size:13px; font-weight:500; color:#334155; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $p->nama_produk }}</span>
                            <span style="font-size:12px; font-weight:700; color:#63A2BB; flex-shrink:0;">{{ number_format($p->total_terjual) }} terjual</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="text-align:right;">
            <a href="{{ route('admin.master-product.index') }}" class="btn-primary">Lihat Semua Produk →</a>
        </div>
    </div>

    {{-- ===== KATEGORI TAB ===== --}}
    <div x-show="activeFilter === 'kategori'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        @php
            $totalKat    = \App\Models\Kategori::count();
            $katLevel1   = \App\Models\Kategori::where('level',1)->count();
            $katLevel2   = \App\Models\Kategori::where('level',2)->count();
            $katLevel3   = \App\Models\Kategori::where('level',3)->count();
            $kats        = \App\Models\Kategori::where('level',1)->withCount('produk')->get();
            $totalProdukKat = $kats->sum('produk_count');
        @endphp

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px;">
            @foreach([
                ['label'=>'Total Kategori','val'=>$totalKat,'color'=>'#63A2BB'],
                ['label'=>'Level 1','val'=>$katLevel1,'color'=>'#2563EB'],
                ['label'=>'Level 2 (Sub)','val'=>$katLevel2,'color'=>'#7C3AED'],
                ['label'=>'Level 3 (Sub-sub)','val'=>$katLevel3,'color'=>'#D97706'],
            ] as $st)
            <div class="stat-card" style="padding:18px;">
                <p style="font-size:28px; font-weight:900; color:{{ $st['color'] }}; margin:0 0 6px;">{{ $st['val'] }}</p>
                <p style="font-size:11px; font-weight:600; color:#94A3B8; margin:0;">{{ $st['label'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-header"><p class="panel-title">Distribusi Produk per Kategori Utama</p></div>
            <div class="panel-body">
                @foreach($kats as $kat)
                    @php $pct = $totalProdukKat > 0 ? round($kat->produk_count/$totalProdukKat*100) : 0; @endphp
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <span style="font-size:13px; font-weight:600; color:#334155; width:80px; flex-shrink:0;">{{ $kat->nama_kategori }}</span>
                        <div style="flex:1; height:10px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                            <div style="height:10px; background:linear-gradient(90deg,#63A2BB,#4A8BA3); border-radius:99px; width:{{ $pct }}%; transition:width 0.6s;"></div>
                        </div>
                        <span style="font-size:12px; font-weight:700; color:#334155; width:60px; text-align:right;">{{ $kat->produk_count }} produk</span>
                        <span style="font-size:11px; color:#94A3B8; width:30px; text-align:right;">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="text-align:right;">
            <a href="{{ route('admin.category.index') }}" class="btn-primary">Kelola Semua Kategori →</a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#94A3B8';

    const trendLabels = @json($trendData->pluck('day')->map(fn($d) => (string) $d));
    const trendRevenue = @json($trendData->pluck('revenue')->map(fn($v) => (float) $v));
    const prevRevenue = @json($prevTrendData->pluck('revenue')->map(fn($v) => (float) $v));

    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    { label: 'Revenue', data: trendRevenue, borderColor: '#63A2BB', backgroundColor: 'rgba(99,162,187,0.1)', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#63A2BB', borderWidth: 2 },
                    { label: 'Previous', data: prevRevenue, borderColor: '#CBD5E1', backgroundColor: 'rgba(203,213,225,0.08)', tension: 0.4, pointRadius: 0, borderDash: [5,4], borderWidth: 1.5 },
                ],
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#F1F5F9' } } } },
        });
    }

    const donutLabels = @json($categoryRevenue->pluck('category'));
    const donutData = @json($categoryRevenue->pluck('revenue')->map(fn($v) => (float) $v));
    const donutColors = ['#63A2BB','#14b8a6','#06b6d4','#22c55e','#3b82f6','#a78bfa','#f97316','#ef4444'];
    const ctxDonut = document.getElementById('donutChart');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: { labels: donutLabels, datasets: [{ data: donutData, backgroundColor: donutLabels.map((_,i) => donutColors[i%donutColors.length]), borderWidth: 0, hoverOffset: 4 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, cutout: '70%' },
        });
    }

    const monthLabels = @json(array_map(fn($x) => $x['month'], $monthly));
    const monthThis = @json(array_map(fn($x) => (float) $x['this'], $monthly));
    const monthLast = @json(array_map(fn($x) => (float) $x['last'], $monthly));
    const ctxBar = document.getElementById('monthlyBarChart');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [
                    { label: 'This Year', data: monthThis, backgroundColor: 'rgba(99,162,187,0.7)', borderRadius: 6, borderSkipped: false },
                    { label: 'Last Year', data: monthLast, backgroundColor: 'rgba(203,213,225,0.5)', borderRadius: 6, borderSkipped: false },
                ],
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#F1F5F9' } } } },
        });
    }
</script>
@endsection

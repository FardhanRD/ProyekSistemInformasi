@extends('layouts.admin')

@section('title', 'Report & Analytics')

@section('content')
@php
    $revenueLabels = collect($revenueRows)->pluck('periode');
    $revenueValues = collect($revenueRows)->pluck('revenue');
    $productLabels = $topProducts->pluck('nama_produk');
    $productValues = $topProducts->pluck('total_terjual');
    $stockLabels = collect($stockStats['stockMovement'] ?? [])->pluck('periode');
    $stockIn = collect($stockStats['stockMovement'] ?? [])->pluck('total_in');
    $stockOut = collect($stockStats['stockMovement'] ?? [])->pluck('total_out');
@endphp

<div style="padding: 28px 28px 40px;" x-data="{ tab: '{{ $tab }}' }">

    {{-- Page Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <p style="font-size:11px; font-weight:700; color:#63A2BB; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 4px;">Analitik</p>
            <h1 class="page-header-title" style="margin:0 0 4px;">Report & Analytics</h1>
            <p class="page-header-sub" style="margin:0; color:#94A3B8;">Pantau perkembangan revenue, produk, customer, dan pergerakan stok.</p>
        </div>
        
        {{-- Elegant Tab Navigation --}}
        <div style="display:flex; background:white; padding:4px; border-radius:12px; border:1px solid #E2E8F0; gap:2px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <button @click="tab='revenue'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='revenue' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                📈 Revenue
            </button>
            <button @click="tab='produk'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='produk' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                👟 Produk
            </button>
            <button @click="tab='customer'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='customer' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                👤 Customer
            </button>
            <button @click="tab='stok'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='stok' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                📦 Stok
            </button>
        </div>
    </div>

    {{-- ===== REVENUE TAB CONTENT ===== --}}
    <div x-show="tab==='revenue'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            {{-- Date & Export Toolbar --}}
            <div class="panel" style="padding: 16px 20px;">
                <form method="GET" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; justify-content:space-between; margin:0;">
                    <input type="hidden" name="tab" value="revenue">
                    
                    <div style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; flex:1;">
                        <div style="width: 140px;">
                            <label class="form-label" style="margin-bottom: 6px;">Mulai Tanggal</label>
                            <input type="date" name="start" value="{{ $start }}" class="form-input" style="height: 38px;">
                        </div>
                        <div style="width: 140px;">
                            <label class="form-label" style="margin-bottom: 6px;">Sampai Tanggal</label>
                            <input type="date" name="end" value="{{ $end }}" class="form-input" style="height: 38px;">
                        </div>
                        <div style="width: 130px;">
                            <label class="form-label" style="margin-bottom: 6px;">Grup Data</label>
                            <select name="group_by" class="form-input" style="height: 38px; cursor: pointer;">
                                <option value="day" @selected($groupBy==='day')>Per Hari</option>
                                <option value="week" @selected($groupBy==='week')>Per Minggu</option>
                                <option value="month" @selected($groupBy==='month')>Per Bulan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" style="height: 38px; padding: 0 16px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Filter
                        </button>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('admin.report.export', array_merge(request()->except('format'), ['format' => 'excel'])) }}" class="btn-secondary text-emerald-600 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition duration-150" style="height:38px; display:inline-flex; align-items:center; gap:6px; font-weight:700;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Excel Export
                        </a>
                        <a href="{{ route('admin.report.export', array_merge(request()->except('format'), ['format' => 'pdf'])) }}" class="btn-secondary text-rose-600 border-rose-200 bg-rose-50 hover:bg-rose-100 transition duration-150" style="height:38px; display:inline-flex; align-items:center; gap:6px; font-weight:700;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF Export
                        </a>
                    </div>
                </form>
            </div>

            {{-- Line Chart --}}
            <div class="panel" style="padding: 20px;">
                <div style="margin-bottom: 14px;">
                    <span style="font-size:13px; font-weight:700; color:#1E293B;">Revenue Trend Chart</span>
                </div>
                <canvas id="revenueChart" height="90"></canvas>
            </div>

            {{-- Revenue Table --}}
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Tabel Rincian Omset & Transaksi</p></div>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Periode Tanggal</th>
                                <th style="text-align: center; width: 150px;">Jumlah Order</th>
                                <th style="text-align: right; width: 220px;">Revenue / Omset</th>
                                <th style="text-align: right; width: 220px;">Rata-rata Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueRows as $row)
                                <tr>
                                    <td style="font-weight: 600; color: #1E293B;">{{ $row['periode'] }}</td>
                                    <td style="text-align: center; font-weight: 700; color: #475569;">{{ $row['jumlah_order'] }}</td>
                                    <td style="text-align: right; font-weight: 800; color: #63A2BB; font-family: monospace;">
                                        Rp {{ number_format($row['revenue'],0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #0F172A; font-family: monospace;">
                                        Rp {{ number_format($row['avg_order_value'],0,',','.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #94A3B8; padding: 36px;">Tidak ada riwayat transaksi pada periode terpilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== PRODUK TAB CONTENT ===== --}}
    <div x-show="tab==='produk'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            {{-- Product Chart --}}
            <div class="panel" style="padding: 20px;">
                <div style="margin-bottom: 14px;">
                    <span style="font-size:13px; font-weight:700; color:#1E293B;">Top Product Sales Chart</span>
                </div>
                <canvas id="productChart" height="90"></canvas>
            </div>

            {{-- Products Table --}}
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Produk Terlaris</p></div>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 70px; text-align: center;">Rank</th>
                                <th>Nama Produk</th>
                                <th style="text-align: center; width: 180px;">Total Unit Terjual</th>
                                <th style="text-align: center; width: 150px;">Rating Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $product)
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; font-size: 11px; font-weight: 800; {{ $index == 0 ? 'background:#FEF3C7; color:#D97706; border: 1.5px solid #FCD34D;' : ($index == 1 ? 'background:#F1F5F9; color:#475569;' : 'background:#FAFAFA; color:#94A3B8;') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: #0F172A;">{{ $product->nama_produk }}</td>
                                    <td style="text-align: center; font-weight: 700; color: #63A2BB;">{{ number_format($product->total_terjual) }} unit</td>
                                    <td style="text-align: center;">
                                        <span style="color:#F59E0B; font-weight:700;">{{ number_format($product->rata_rating, 1) }} ★</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada produk yang terjual.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== CUSTOMER TAB CONTENT ===== --}}
    <div x-show="tab==='customer'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            {{-- KPI Statistics Grid --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Repeat Buyer Rate</p>
                        <p style="font-size: 26px; font-weight: 900; color: #63A2BB; margin: 0;">{{ $customerStats['repeatBuyerRate'] ?? 0 }}%</p>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #EFF8FB; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" fill="none" stroke="#63A2BB" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18m0 0V5"/></svg>
                    </div>
                </div>

                <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Customer Baru Terdaftar</p>
                        <p style="font-size: 26px; font-weight: 900; color: #0F172A; margin: 0;">{{ $customerStats['newCustomers']->sum('total') ?? 0 }}</p>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #F0FDF4; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" fill="none" stroke="#16A34A" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                </div>

                <div class="panel" style="padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; margin: 0 0 6px;">Top Customer Teraktif</p>
                        <p style="font-size: 16px; font-weight: 800; color: #1E293B; margin: 0; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $customerStats['topCustomers']->first()?->pengguna?->nama_pengguna ?? '-' }}
                        </p>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #FEF3C7; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Top Customers Table --}}
            <div class="panel">
                <div class="panel-header"><p class="panel-title">Pelanggan dengan Nilai Belanja Tertinggi</p></div>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th style="text-align: center; width: 180px;">Jumlah Transaksi Order</th>
                                <th style="text-align: right; width: 250px;">Total Nilai Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerStats['topCustomers'] as $row)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: #0F172A;">{{ $row->pengguna?->nama_pengguna ?? '-' }}</div>
                                        <div style="font-size: 11px; color: #94A3B8;">{{ $row->pengguna?->email ?? '-' }}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: #475569;">{{ $row->total_order }} kali</td>
                                    <td style="text-align: right; font-weight: 800; color: #0F172A; font-family: monospace;">
                                        Rp {{ number_format($row->total_belanja ?? 0,0,',','.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada customer bertransaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== STOK TAB CONTENT ===== --}}
    <div x-show="tab==='stok'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            {{-- Stock Movement Chart --}}
            <div class="panel" style="padding: 20px;">
                <div style="margin-bottom: 14px;">
                    <span style="font-size:13px; font-weight:700; color:#1E293B;">Stock In vs Stock Out Flow</span>
                </div>
                <canvas id="stockChart" height="90"></canvas>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                
                {{-- Low Stock Products --}}
                <div class="panel">
                    <div class="panel-header"><p class="panel-title" style="color: #EF4444;">⚠️ Peringatan Stok Menipis</p></div>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Produk Varian</th>
                                    <th style="text-align: center; width: 100px;">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockStats['lowStockProducts'] as $item)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #1E293B;">{{ $item->produk?->nama_produk ?? '-' }}</div>
                                            <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                                SKU: {{ $item->sku ?: '-' }} | {{ $item->warna?->nama_warna ?? '-' }}
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-danger" style="font-size: 12px; font-weight: 800; min-width: 40px; text-align: center;">{{ $item->stok }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; color: #94A3B8; padding: 24px;">Semua stok aman / cukup.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Inventory Value per Category --}}
                <div class="panel">
                    <div class="panel-header"><p class="panel-title">Nilai Aset Inventori per Kategori</p></div>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th style="text-align: right; width: 180px;">Nilai Aset Buku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockStats['inventoryValue'] as $row)
                                    <tr>
                                        <td style="font-weight: 700; color: #334155;">{{ $row->nama_kategori }}</td>
                                        <td style="text-align: right; font-weight: 800; color: #0F172A; font-family: monospace;">
                                            Rp {{ number_format($row->nilai_inventori,0,',','.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; color: #94A3B8; padding: 24px;">Tidak ada kategori terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueLabels = @json($revenueLabels);
const revenueValues = @json($revenueValues);
const productLabels = @json($productLabels);
const productValues = @json($productValues);
const stockLabels = @json($stockLabels);
const stockIn = @json($stockIn);
const stockOut = @json($stockOut);

function renderCharts() {
    // Override Default Font settings to Match Layout
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#94A3B8';

    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) {
        new Chart(revenueEl, { 
            type: 'line', 
            data: { 
                labels: revenueLabels, 
                datasets: [{ 
                    label: 'Omset Omset', 
                    data: revenueValues, 
                    borderColor: '#63A2BB', 
                    backgroundColor: 'rgba(99,162,187,0.1)', 
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#63A2BB'
                }] 
            }, 
            options: { 
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false } }, 
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' } } 
                }
            } 
        });
    }
    
    const productEl = document.getElementById('productChart');
    if (productEl) {
        new Chart(productEl, { 
            type: 'bar', 
            data: { 
                labels: productLabels, 
                datasets: [{ 
                    label: 'Total Terjual', 
                    data: productValues, 
                    backgroundColor: '#63A2BB',
                    borderRadius: 6
                }] 
            }, 
            options: { 
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false } }, 
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' } } 
                }
            } 
        });
    }
    
    const stockEl = document.getElementById('stockChart');
    if (stockEl) {
        new Chart(stockEl, { 
            type: 'line', 
            data: { 
                labels: stockLabels, 
                datasets: [ 
                    { 
                        label: 'Stok Masuk', 
                        data: stockIn, 
                        borderColor: '#10B981', 
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        borderWidth: 2
                    }, 
                    { 
                        label: 'Stok Keluar', 
                        data: stockOut, 
                        borderColor: '#EF4444', 
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        borderWidth: 2
                    } 
                ] 
            }, 
            options: { 
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { 
                    x: { grid: { display: false } }, 
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' } } 
                }
            } 
        });
    }
}

document.addEventListener('DOMContentLoaded', renderCharts);
</script>
@endsection

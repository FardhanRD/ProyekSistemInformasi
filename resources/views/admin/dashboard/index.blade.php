@extends('layouts.admin')

@section('title', 'Dashboard Analytics')

@section('content')
<div class="mx-auto max-w-7xl p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#63a2bb]">MOVR Admin</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900">Dashboard Analytics</h1>
            <p class="mt-2 text-sm text-slate-600">Tampilan analitik transaksi untuk admin.</p>
        </div>

        <div class="w-full sm:w-auto">
            <form method="GET" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-slate-500">Start</label>
                    <input type="date" name="start" value="{{ request('start', $start->toDateString()) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-slate-500">End</label>
                    <input type="date" name="end" value="{{ request('end', $end->toDateString()) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700">Filter</button>
                <a href="{{ url('/admin/dashboard/export') }}?start={{ request('start', $start->toDateString()) }}&end={{ request('end', $end->toDateString()) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">Export Report</a>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $revUp = ($revenueChange >= 0);
            $ordUp = ($ordersChange >= 0);
            $avgUp = ($avgChange >= 0);
        @endphp

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Total Revenue</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $revUp ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                    {{ ($revUp ? '+' : '') . number_format($revenueChange, 2) }}%
                </span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Total Orders</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $totalOrders }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $ordUp ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-600' }}">
                    {{ ($ordUp ? '+' : '') . number_format($ordersChange, 2) }}%
                </span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Active Customers</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ $activeCustomers }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-green-50 text-green-600">-</span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Avg Order Value</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">Rp {{ number_format($avgOrderValue ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $avgUp ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                    {{ ($avgUp ? '+' : '') . number_format($avgChange, 2) }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-slate-800">Performance Trends</p>
                <span class="text-xs text-slate-500">Revenue: Aktif vs Previous</span>
            </div>
            <div class="mt-4">
                <canvas id="trendChart" height="120"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-slate-800">Sales Distribution</p>
                <span class="text-xs text-slate-500">Donut by Category</span>
            </div>
            <div class="mt-4 flex gap-4">
                <div class="w-44">
                    <canvas id="donutChart" height="140"></canvas>
                </div>
                <div class="flex-1">
                    <ul class="space-y-2 text-sm" id="donutLegend">
                        @foreach($categoryRevenue as $i)
                            <li class="flex items-center gap-2">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-teal-600"></span>
                                <span class="text-slate-700">{{ $i->category }}</span>
                                <span class="ml-auto text-slate-900 font-semibold">Rp {{ number_format($i->revenue,0,',','.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-sm font-bold text-slate-800">Monthly Revenue Comparison</p>
            <span class="text-xs text-slate-500">This Year vs Last Year</span>
        </div>
        <div class="mt-4">
            <canvas id="monthlyBarChart" height="110"></canvas>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-sm font-bold text-slate-800">Recent Activities</p>
            <span class="text-xs text-slate-500">Last 10 actions</span>
        </div>

        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="py-3">Timestamp</th>
                        <th class="py-3">Deskripsi</th>
                        <th class="py-3">Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities as $log)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 text-slate-700">{{ $log->timestamp ?? '-' }}</td>
                            <td class="py-3 text-slate-700">{{ $log->description ?? '-' }}</td>
                            <td class="py-3 text-slate-700">{{ $log->admin_name ?? ($log->admin ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr class="border-t border-slate-100">
                            <td class="py-3 text-slate-600" colspan="3">Belum ada data admin_log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Performance Trends
    const trendLabels = @json($trendData->pluck('day')->map(fn($d)=> (string)$d));
    const trendRevenue = @json($trendData->pluck('revenue')->map(fn($v)=> (float)$v));

    const prevLabels = @json($prevTrendData->pluck('day')->map(fn($d)=> (string)$d));
    const prevRevenue = @json($prevTrendData->pluck('revenue')->map(fn($v)=> (float)$v));

const ctxTrend = document.getElementById('trendChart');
    const prevDataset = { label: 'Previous', data: prevRevenue, borderColor: '#64748b', backgroundColor: 'rgba(100,116,139,0.12)', tension: 0.3 };
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: trendRevenue,
                        borderColor: '#0d9488',
                        backgroundColor: 'rgba(13,148,136,0.15)',
                        tension: 0.3
                    },
                    prevDataset
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    // Donut
    const donutLabels = @json($categoryRevenue->pluck('category'));
    const donutData = @json($categoryRevenue->pluck('revenue')->map(fn($v)=>(float)$v));

    const donutColors = [
        '#0d9488','#14b8a6','#06b6d4','#22c55e','#3b82f6','#a78bfa','#f97316','#ef4444'
    ];

    const ctxDonut = document.getElementById('donutChart');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutLabels.map((_, i)=> donutColors[i % donutColors.length])
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Monthly bar
    const monthLabels = @json(array_map(fn($x)=>$x['month'], $monthly));
    const monthThis = @json(array_map(fn($x)=>(float)$x['this'], $monthly));
    const monthLast = @json(array_map(fn($x)=>(float)$x['last'], $monthly));

    const ctxBar = document.getElementById('monthlyBarChart');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [
                    { label: 'This Year', data: monthThis, backgroundColor: 'rgba(13,148,136,0.55)' },
                    { label: 'Last Year', data: monthLast, backgroundColor: 'rgba(100,116,139,0.35)' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@endsection


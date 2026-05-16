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
<div class="space-y-6" x-data="{ tab: '{{ $tab }}' }">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Report & Analytics</h1>
                <p class="text-slate-600">Revenue, produk, customer, dan stok.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button @click="tab='revenue'" class="px-4 py-2 rounded-xl text-sm font-semibold" :class="tab==='revenue' ? 'bg-[#2B9BAF] text-white' : 'bg-slate-100 text-slate-700'">Revenue</button>
                <button @click="tab='produk'" class="px-4 py-2 rounded-xl text-sm font-semibold" :class="tab==='produk' ? 'bg-[#2B9BAF] text-white' : 'bg-slate-100 text-slate-700'">Produk</button>
                <button @click="tab='customer'" class="px-4 py-2 rounded-xl text-sm font-semibold" :class="tab==='customer' ? 'bg-[#2B9BAF] text-white' : 'bg-slate-100 text-slate-700'">Customer</button>
                <button @click="tab='stok'" class="px-4 py-2 rounded-xl text-sm font-semibold" :class="tab==='stok' ? 'bg-[#2B9BAF] text-white' : 'bg-slate-100 text-slate-700'">Stok</button>
            </div>
        </div>
    </div>

    <template x-if="tab==='revenue'">
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <input type="hidden" name="tab" value="revenue">
                    <input type="date" name="start" value="{{ $start }}" class="rounded-xl border px-4 py-2 text-sm">
                    <input type="date" name="end" value="{{ $end }}" class="rounded-xl border px-4 py-2 text-sm">
                    <select name="group_by" class="rounded-xl border px-4 py-2 text-sm">
                        <option value="day" @selected($groupBy==='day')>Hari</option>
                        <option value="week" @selected($groupBy==='week')>Minggu</option>
                        <option value="month" @selected($groupBy==='month')>Bulan</option>
                    </select>
                    <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold">Filter</button>
                    <a class="rounded-xl bg-slate-900 px-4 py-2 text-white font-semibold" href="{{ route('admin.report.export', array_merge(request()->except('format'), ['format' => 'excel'])) }}">Export Excel</a>
                    <a class="rounded-xl bg-amber-500 px-4 py-2 text-white font-semibold" href="{{ route('admin.report.export', array_merge(request()->except('format'), ['format' => 'pdf'])) }}">Export PDF</a>
                </form>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><canvas id="revenueChart" height="100"></canvas></div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50"><tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Jumlah Order</th><th class="px-4 py-3">Revenue</th><th class="px-4 py-3">Avg Order Value</th></tr></thead>
                    <tbody>
                    @forelse($revenueRows as $row)
                        <tr class="border-t border-slate-100"><td class="px-4 py-3">{{ $row['periode'] }}</td><td class="px-4 py-3">{{ $row['jumlah_order'] }}</td><td class="px-4 py-3">Rp {{ number_format($row['revenue'],0,',','.') }}</td><td class="px-4 py-3">Rp {{ number_format($row['avg_order_value'],0,',','.') }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Tidak ada data revenue.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <template x-if="tab==='produk'">
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><canvas id="productChart" height="100"></canvas></div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50"><tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">#</th><th class="px-4 py-3">Produk</th><th class="px-4 py-3">Total Terjual</th><th class="px-4 py-3">Rata Rating</th></tr></thead>
                    <tbody>
                    @forelse($topProducts as $index => $product)
                        <tr class="border-t border-slate-100"><td class="px-4 py-3">{{ $index + 1 }}</td><td class="px-4 py-3">{{ $product->nama_produk }}</td><td class="px-4 py-3">{{ $product->total_terjual }}</td><td class="px-4 py-3">{{ $product->rata_rating }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Tidak ada data produk.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <template x-if="tab==='customer'">
        <div class="space-y-4">
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-600">Repeat Buyer Rate</p><p class="text-3xl font-bold text-[#2B9BAF]">{{ $customerStats['repeatBuyerRate'] ?? 0 }}%</p></div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-600">Customer Baru</p><p class="text-3xl font-bold text-slate-900">{{ $customerStats['newCustomers']->sum('total') ?? 0 }}</p></div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-600">Top Customer</p><p class="text-2xl font-bold text-slate-900">{{ $customerStats['topCustomers']->first()?->pengguna?->nama_pengguna ?? '-' }}</p></div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50"><tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Order</th><th class="px-4 py-3">Belanja</th></tr></thead>
                    <tbody>
                    @forelse($customerStats['topCustomers'] as $row)
                        <tr class="border-t border-slate-100"><td class="px-4 py-3">{{ $row->pengguna?->nama_pengguna ?? '-' }}</td><td class="px-4 py-3">{{ $row->total_order }}</td><td class="px-4 py-3">Rp {{ number_format($row->total_belanja ?? 0,0,',','.') }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Tidak ada data customer.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <template x-if="tab==='stok'">
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"><canvas id="stockChart" height="100"></canvas></div>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50"><tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Produk Menipis</th><th class="px-4 py-3">Stok</th></tr></thead>
                        <tbody>
                        @forelse($stockStats['lowStockProducts'] as $item)
                            <tr class="border-t border-slate-100"><td class="px-4 py-3">{{ $item->produk?->nama_produk ?? '-' }}</td><td class="px-4 py-3">{{ $item->stok }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-6 text-center text-slate-500">Tidak ada stok menipis.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50"><tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Nilai Inventori</th></tr></thead>
                        <tbody>
                        @forelse($stockStats['inventoryValue'] as $row)
                            <tr class="border-t border-slate-100"><td class="px-4 py-3">{{ $row->nama_kategori }}</td><td class="px-4 py-3">Rp {{ number_format($row->nilai_inventori,0,',','.') }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-6 text-center text-slate-500">Tidak ada data inventori.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
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
    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) {
        new Chart(revenueEl, { type: 'line', data: { labels: revenueLabels, datasets: [{ label: 'Revenue', data: revenueValues, borderColor: '#2B9BAF', backgroundColor: 'rgba(43,155,175,.15)', tension: .3 }] }, options: { responsive: true } });
    }
    const productEl = document.getElementById('productChart');
    if (productEl) {
        new Chart(productEl, { type: 'bar', data: { labels: productLabels, datasets: [{ label: 'Total Terjual', data: productValues, backgroundColor: '#2B9BAF' }] }, options: { responsive: true } });
    }
    const stockEl = document.getElementById('stockChart');
    if (stockEl) {
        new Chart(stockEl, { type: 'line', data: { labels: stockLabels, datasets: [ { label: 'Masuk', data: stockIn, borderColor: '#16a34a' }, { label: 'Keluar', data: stockOut, borderColor: '#dc2626' } ] }, options: { responsive: true } });
    }
}

document.addEventListener('DOMContentLoaded', renderCharts);
</script>
@endsection

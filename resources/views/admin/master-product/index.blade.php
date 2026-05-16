@extends('layouts.admin')

@section('title','Master Product')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Product Master Analysis</h1>
            <p class="text-sm text-slate-500 mt-1">
                Real-time inventory and catalog distribution metrics for {{ now()->isoFormat('MMM D, YYYY') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master-product.export') }}?{{ http_build_query(request()->only(['search','status','gender','start_date','end_date'])) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Export CSV
            </a>
            <a href="{{ route('admin.master-product.export') }}?{{ http_build_query(array_merge(request()->only(['search','status','gender','start_date','end_date']), ['format' => 'pdf'])) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Export PDF
            </a>
            <a href="{{ route('admin.master-product.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">
                + Add Product
            </a>
        </div>
    </div>

    {{-- CALENDAR / DATE RANGE --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-700">From</label>
            <input type="date" id="startDate" name="start_date" value="{{ request('start_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            <label class="text-sm text-slate-700">To</label>
            <input type="date" id="endDate" name="end_date" value="{{ request('end_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm" />
            <button id="applyCalendar" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Apply Calendar</button>
        </div>

        <div id="fullCalendar" class="ml-auto w-80"></div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">ACTIVE MASTER CATALOG</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $total_products }}</div>
                </div>
                <div class="flex flex-col items-end">
                    <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600">
                        +12.5%
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">ACROSS BUSINESS UNITS</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $total_categories }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                    Static
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">ITEMS BELOW THRESHOLD</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $low_stock_alert }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600">
                    Alert
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">CURRENT ASSET ESTIMATE</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">Rp {{ number_format($inventory_value,0,',','.') }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600">
                    +5.2%
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION TENGAH --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-slate-900">Popular Products</h2>
            </div>

            @foreach($popular_products as $p)
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-slate-700">{{ $p->nama_produk }}</span>
                    <span class="text-teal-600 text-sm font-medium">{{ number_format($p->total_terjual) }} Sales</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                    <div class="bg-teal-500 h-2 rounded-full" style="width:{{ $max_terjual > 0 ? ($p->total_terjual / $max_terjual * 100) : 0 }}%"></div>
                </div>
            @endforeach
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Gender Distribution</h2>
            <div class="mt-4">
                <canvas id="genderChart" height="200"></canvas>
            </div>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach($gender_distribution as $g)
                    @php
                        $colors = [
                            'men' => '#2B9BAF',
                            'women' => '#F6C90E',
                            'kids' => '#FF6B6B',
                            'unisex' => '#95E1D3'
                        ];
                        $c = $colors[$g->gender] ?? '#ccc';
                    @endphp
                    <li class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color:{{ $c }}"></span>
                            {{ ucfirst($g->gender) }}
                        </span>
                        <span class="font-semibold text-slate-800">{{ $g->total }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- MASTER PRODUCT LIST --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="font-bold text-slate-900">Master Product List</h2>

            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by product name..." form="masterProductFilters" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-full sm:w-64" />

                <form id="masterProductFilters" method="GET" action="{{ route('admin.master-product.index') }}" class="hidden"></form>

                <form method="GET" action="{{ route('admin.master-product.index') }}" class="flex gap-2">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Status (All)</option>
                        <option value="publish" {{ $status_filter === 'publish' ? 'selected' : '' }}>Publish</option>
                        <option value="draft" {{ $status_filter === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ $status_filter === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                    <select name="gender" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Gender (All)</option>
                        <option value="men" {{ $gender_filter === 'men' ? 'selected' : '' }}>Men</option>
                        <option value="women" {{ $gender_filter === 'women' ? 'selected' : '' }}>Women</option>
                        <option value="unisex" {{ $gender_filter === 'unisex' ? 'selected' : '' }}>Unisex</option>
                        <option value="kids" {{ $gender_filter === 'kids' ? 'selected' : '' }}>Kids</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Filter</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-2">ID</th>
                        <th class="py-3 px-2">IMAGE</th>
                        <th class="py-3 px-2">PRODUCT NAME</th>
                        <th class="py-3 px-2">CATEGORY</th>
                        <th class="py-3 px-2">SUPPLIER</th>
                        <th class="py-3 px-2">GENDER</th>
                        <th class="py-3 px-2">SPORT TYPE</th>
                        <th class="py-3 px-2">PRICE</th>
                        <th class="py-3 px-2">STATUS</th>
                        <th class="py-3 px-2">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk_list as $produk)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-2">{{ $produk->formatted_id }}</td>
                            <td class="py-3 px-2">
                                @if($produk->gambarUtama)
                                    <img src="{{ Storage::url($produk->gambarUtama->url_gambar) }}" class="w-10 h-10 rounded object-cover" alt="{{ $produk->nama_produk }}" />
                                @else
                                    <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">—</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                <div class="font-medium">{{ $produk->nama_produk }}</div>
                                <div class="text-xs text-gray-400">{{ $produk->slug }}</div>
                            </td>
                            <td class="py-3 px-2">{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $produk->supplier->nama_toko ?? '-' }}</td>
                            <td class="py-3 px-2">{{ ucfirst($produk->gender) }}</td>
                            <td class="py-3 px-2">{{ $produk->tipe_olahraga ?? '-' }}</td>
                            <td class="py-3 px-2">Rp {{ number_format($produk->harga_dasar,0,',','.') }}</td>
                            <td class="py-3 px-2">
                                @php $status = $produk->status_stok; @endphp
                                @if($status === 'available')
                                    <span class="text-green-600 font-semibold text-sm">AVAILABLE</span>
                                @elseif($status === 'low_stock')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full font-medium">LOW STOCK</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">OUT OF STOCK</span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                <div class="flex gap-2 items-center">
                                    <a href="{{ route('admin.master-product.detail', $produk->produk_id) }}" class="text-slate-600" title="View">👁</a>
                                    <a href="{{ route('admin.master-product.edit', $produk->produk_id) }}" class="text-slate-600" title="Edit">✏</a>
                                    <form method="POST" action="{{ route('admin.master-product.destroy', $produk->produk_id) }}" onsubmit="return confirm('Nonaktifkan produk ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-600" title="Hapus">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $produk_list->links() }}
        <p class="text-sm text-gray-500 mt-2">
            Showing {{ $produk_list->firstItem() }}–{{ $produk_list->lastItem() }} of {{ $produk_list->total() }} items
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const genderData = @json($gender_distribution);
const labels = genderData.map(d => d.gender);
const data = genderData.map(d => d.total);
const colors = {
  men:'#2B9BAF', women:'#F6C90E',
  kids:'#FF6B6B', unisex:'#95E1D3'
};

const el = document.getElementById('genderChart');
if (el) {
  new Chart(el, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: labels.map(l => colors[l] || '#ccc'),
        borderWidth: 0
      }]
    },
    options: {
      cutout: '70%',
      plugins: { legend: { display: false } }
    }
  });
}
</script>
<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('fullCalendar');
    const startInput = document.getElementById('startDate');
    const endInput = document.getElementById('endDate');
    const applyBtn = document.getElementById('applyCalendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: '2026-05-01',
        height: 350,
        selectable: true,
        headerToolbar: { left: '', center: 'title', right: '' },
        select: function(info) {
            // set inputs to selected range
            startInput.value = info.startStr;
            // endStr is exclusive in FullCalendar's select -> use previous day
            const endDate = new Date(info.end);
            endDate.setDate(endDate.getDate() - 1);
            endInput.value = endDate.toISOString().slice(0,10);
        },
        eventClick: function(info) {
            // event has date in info.event.startStr
            const d = info.event.startStr.slice(0,10);
            startInput.value = d; endInput.value = d;
            // apply immediately
            const params = new URLSearchParams(window.location.search);
            params.set('start_date', d); params.set('end_date', d);
            window.location = `${window.location.pathname}?${params.toString()}`;
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams({ start: fetchInfo.startStr, end: fetchInfo.endStr });
            fetch(`{{ route('admin.master-product.events') }}?` + params.toString())
                .then(r => r.json())
                .then(data => {
                    const evs = data.map(e => ({
                        title: e.type === 'new' ? (e.count + ' New') : (e.count + ' Campaign'),
                        start: e.date,
                        allDay: true,
                        color: e.type === 'new' ? '#16a34a' : '#2563eb'
                    }));
                    successCallback(evs);
                }).catch(failureCallback);
        }
    });

    calendar.render();

    applyBtn?.addEventListener('click', () => {
        const s = startInput.value;
        const e = endInput.value;
        const params = new URLSearchParams(window.location.search);
        if (s) params.set('start_date', s); else params.delete('start_date');
        if (e) params.set('end_date', e); else params.delete('end_date');
        window.location = `${window.location.pathname}?${params.toString()}`;
    });
});
</script>
@endsection


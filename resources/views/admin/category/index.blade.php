@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Category Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola kategori utama & sub kategori.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="#" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export</a>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]" onclick="document.getElementById('addCategoryModal').classList.remove('hidden')">
                + Add Category
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">TOTAL CATEGORIES</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $total_categories_active ?? 0 }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600">+3 new</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">ACTIVE PRODUCTS</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $active_products ?? 0 }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Static</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">AVG MARGIN</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">-</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700">TBD</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div id="fullCalendarCat" class="text-sm" style="height:200px;"></div>
        </div>
    </div>

    {{-- Favorite Categories (cards) --}}
    <div class="mb-6">
        <h2 class="font-bold text-slate-900 mb-3">Favorite Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @forelse(($favorite_categories ?? collect()) as $cat)
                @php
                    // Simple threshold based on count
                    $inv = $cat->inventory_count ?? 0;
                    $status = $inv >= 50 ? 'Stable' : ($inv >= 20 ? 'Low' : 'Critical');
                    $color = $status === 'Stable' ? 'bg-green-50 text-green-700' : ($status === 'Low' ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700');
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-bold text-slate-900">{{ $cat->nama_kategori }}</div>
                            <div class="text-sm text-slate-500 mt-1">Inventory: {{ $inv }}</div>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full {{ $color }}">{{ $status }}</span>
                    </div>
                </div>
            @empty
                <div class="text-sm text-slate-500">Belum ada data kategori.</div>
            @endforelse
        </div>
    </div>

    {{-- Category Hierarchy Distribution --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900">Category Hierarchy Distribution</h2>
        </div>

        @forelse(($top_level ?? collect()) as $lvl1)
            <div class="mb-5">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-slate-900">{{ $lvl1->nama_kategori }}</div>
                    <div class="text-xs text-slate-500">Sub-categories: {{ isset($level2_grouped[$lvl1->kategori_id]) ? $level2_grouped[$lvl1->kategori_id]->count() : 0 }}</div>
                </div>

                @php
                    $subs = $level2_grouped[$lvl1->kategori_id] ?? collect();
                @endphp
                <div class="mt-3 overflow-x-auto">
                    <div class="flex gap-3">
                        @forelse($subs as $sub)
                            @php
                                $totalProducts = max(1, $subs->sum('count'));
                                $pct = $sub['count'] / $totalProducts * 100;
                            @endphp
                            <div class="min-w-[220px] rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-slate-900">{{ $sub['nama'] }}</div>
                                    <div class="text-xs text-slate-500">{{ number_format($pct,1) }}%</div>
                                </div>
                                <div class="mt-2 h-2 w-full bg-white rounded-full overflow-hidden">
                                    <div class="h-full bg-teal-500" style="width:{{ min(100,$pct) }}%"></div>
                                </div>
                                <div class="mt-2 text-xs text-slate-600">{{ $sub['count'] }} products</div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">No sub categories.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="text-sm text-slate-500">Belum ada data kategori hierarchy.</div>
        @endforelse
    </div>

    {{-- Master Category Explorer table (dengan PRODUK) --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="font-bold text-slate-900">Master Category Explorer</h2>

            <form method="GET" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end">
                <input type="text" name="q" placeholder="Search by category or product name" value="{{ request('q') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-full sm:w-64">

                <select name="category_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    @foreach(($top_level ?? collect()) as $cat)
                        <option value="{{ $cat->kategori_id }}" {{ request('category_id') == $cat->kategori_id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700">Apply</button>
            </form>
        </div>

        {{-- Tabel: produk per kategori --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-2">ID</th>
                        <th class="py-3 px-2">PRODUCT</th>
                        <th class="py-3 px-2">CATEGORY</th>
                        <th class="py-3 px-2">SPECS</th>
                        <th class="py-3 px-2">GENDER</th>
                        <th class="py-3 px-2">SPORTS TYPE</th>
                        <th class="py-3 px-2">PRICE</th>
                        <th class="py-3 px-2">STATUS</th>
                        <th class="py-3 px-2">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories ?? collect() as $prod)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-2">{{ $prod->formatted_id ?? $prod->produk_id }}</td>
                            <td class="py-3 px-2">
                                <div class="font-medium text-slate-900">{{ $prod->nama_produk }}</div>
                                <div class="text-xs text-slate-500">{{ $prod->slug ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-2">{{ $prod->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $prod->spesifikasi ?? '-' }}</td>
                            <td class="py-3 px-2">{{ ucfirst($prod->gender ?? '-') }}</td>
                            <td class="py-3 px-2">{{ $prod->tipe_olahraga ?? '-' }}</td>
                            <td class="py-3 px-2">Rp {{ number_format($prod->harga_dasar ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2">
                                @if(($prod->is_active ?? 0) == 1)
                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full font-medium">ACTIVE</span>
                                @else
                                    <span class="bg-red-50 text-red-700 text-xs px-2 py-1 rounded-full font-medium">INACTIVE</span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                <div class="flex gap-2 items-center">
                                    <a href="{{ route('admin.master-product.detail', $prod->produk_id) }}" class="text-slate-600" title="View">👁</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-2 text-slate-600" colspan="9">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- pagination --}}
        @if(method_exists($categories ?? null,'links'))
            <div class="mt-4">{{ $categories->links() }}</div>
        @endif
    </div>
</div>

{{-- Add Category Modal (simple) --}}
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black/30 z-50">
    <div class="relative w-full max-w-3xl mx-auto mt-14 bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Add Category</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('addCategoryModal').classList.add('hidden')">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.category.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Nama Kategori</label>
                    <input type="text" name="nama_kategori" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Slug (optional)</label>
                    <input type="text" name="slug" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Parent Kategori</label>
                    <select name="parent_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">(none)</option>
                        @foreach(($top_level ?? collect()) as $cat)
                            <option value="{{ $cat->kategori_id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-500">Banner URL (optional)</label>
                    <input type="text" name="banner_url" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-500">Status</label>
                    <select name="is_active" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('addCategoryModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold hover:bg-teal-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- FullCalendar CSS + JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('fullCalendarCat');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    initialDate: '2026-05-01',
    height: 300,
    headerToolbar: { left: 'prev next today', center: 'title', right: '' },
    events: function(fetchInfo, successCallback, failureCallback) {
      const params = new URLSearchParams({ start: fetchInfo.startStr, end: fetchInfo.endStr });
      fetch(`{{ route('admin.category.events') }}?` + params.toString())
        .then(r => r.json())
        .then(data => {
          const evs = data.map(e => ({
            title: e.count + ' new',
            start: e.date,
            allDay: true,
            color: '#16a34a'
          }));
          successCallback(evs);
        }).catch(failureCallback);
    }
  });

  calendar.render();
});
</script>
@endsection


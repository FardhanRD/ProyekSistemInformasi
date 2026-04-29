@extends('movr.layouts.admin')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $popularMax = max(1, (int) collect($stats['popularProducts'] ?? [])->max('total_sales'));
    $womenPercent = (int) ($stats['genderDistribution']['women'] ?? 65);
    $menPercent = max(0, 100 - $womenPercent);
    $dominantPercent = max($womenPercent, $menPercent);
    $dominantLabel = $womenPercent >= $menPercent ? 'Cewe' : 'Cowo';

    $calendarBaseDate = ($calendarMonthDate ?? now())->copy()->startOfMonth();
    $calendarSelectedDate = \Carbon\Carbon::parse($calendarDate ?? now()->toDateString());
    if (!$calendarSelectedDate->isSameMonth($calendarBaseDate)) {
        $calendarSelectedDate = $calendarBaseDate->copy()->startOfMonth();
    }
    $calendarStart = $calendarBaseDate->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $calendarEnd = $calendarBaseDate->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $calendarDays = [];
    for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
        $calendarDays[] = $day->copy();
    }

    $calendarYears = range(now()->year - 5, now()->year + 1);

    $genderPool = ['Cewe', 'Cowo'];
    $sportPool = ['Lari', 'Lifestyle', 'Casual', 'Training', 'Futsal'];
@endphp

<style>
    .pm-page {
        color: #293444;
    }

    .pm-card {
        border: 1px solid #e6e9f0;
        border-radius: 10px;
        background: #fff;
    }

    .pm-kpi-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .pm-scrollbar::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .pm-scrollbar::-webkit-scrollbar-thumb {
        background: #bdc4d1;
        border-radius: 99px;
    }

    .pm-table-scroll {
        max-height: 540px;
        overflow: auto;
    }

    .pm-table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fcfdff;
    }
</style>

<section class="pm-page space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[30px] font-extrabold leading-none text-[#24344b]">Product Master Analysis</h1>
            <p class="mt-1 text-xs font-semibold text-[#8f98aa]">Real-time inventory and catalog distribution metrics for Oct 24, 2023</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.report') }}" class="inline-flex items-center gap-1.5 rounded-md border border-[#e2e6ef] bg-white px-3 py-2 text-xs font-bold text-[#5d6a80] hover:bg-[#f8f9fc]">
                <i class="fa-solid fa-download text-[10px]"></i>
                Export Report
            </a>
            <a href="{{ route('admin.produk.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-[#63a2bb] px-3 py-2 text-xs font-bold text-white hover:brightness-95">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Add Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <article class="pm-card p-3.5">
            <div class="flex items-center justify-between">
                <span class="pm-kpi-icon bg-[#ffefef] text-[#c22f36]"><i class="fa-regular fa-box"></i></span>
                <span class="rounded-full bg-[#ecfff4] px-1.5 py-0.5 text-[10px] font-extrabold text-[#08a65a]">+12.5%</span>
            </div>
            <p class="mt-3 text-[11px] font-bold text-[#95a0b5]">Total Products</p>
            <p class="mt-0.5 text-[26px] font-extrabold leading-none text-[#22344b]">{{ number_format($stats['totalProducts'] ?? 0) }}</p>
            <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.08em] text-[#c2c8d5]">Active master catalog</p>
        </article>

        <article class="pm-card p-3.5">
            <div class="flex items-center justify-between">
                <span class="pm-kpi-icon bg-[#eef4ff] text-[#406cd8]"><i class="fa-solid fa-shapes"></i></span>
                <span class="text-[10px] font-extrabold text-[#8f98aa]">Static</span>
            </div>
            <p class="mt-3 text-[11px] font-bold text-[#95a0b5]">Total Categories</p>
            <p class="mt-0.5 text-[26px] font-extrabold leading-none text-[#22344b]">{{ number_format($stats['totalCategories'] ?? 0) }}</p>
            <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.08em] text-[#c2c8d5]">Across business units</p>
        </article>

        <article class="pm-card p-3.5">
            <div class="flex items-center justify-between">
                <span class="pm-kpi-icon bg-[#fff6ec] text-[#d27c24]"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <span class="text-[10px] font-extrabold text-[#d62839]">Alert</span>
            </div>
            <p class="mt-3 text-[11px] font-bold text-[#95a0b5]">Low Stock Alert</p>
            <p class="mt-0.5 text-[26px] font-extrabold leading-none text-[#22344b]">{{ number_format($stats['lowStockCount'] ?? 0) }}</p>
            <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.08em] text-[#c2c8d5]">Items below threshold</p>
        </article>

        <article class="pm-card p-3.5">
            <div class="flex items-center justify-between">
                <span class="pm-kpi-icon bg-[#f4eeff] text-[#8b5bd2]"><i class="fa-solid fa-arrow-trend-up"></i></span>
                <span class="rounded-full bg-[#ecfff4] px-1.5 py-0.5 text-[10px] font-extrabold text-[#08a65a]">+5.2%</span>
            </div>
            <p class="mt-3 text-[11px] font-bold text-[#95a0b5]">Inventory Value</p>
            <p class="mt-0.5 text-[26px] font-extrabold leading-none text-[#22344b]">{{ $formatRupiah($stats['inventoryValue'] ?? 0) }}</p>
            <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.08em] text-[#c2c8d5]">Current asset estimate</p>
        </article>
    </div>

    <div class="grid gap-3 xl:grid-cols-[1.45fr_1fr_1fr]">
        <article class="pm-card p-3.5">
            <h2 class="text-[24px] font-extrabold leading-none text-[#24344b]">Produk Populer</h2>
            <div class="mt-4 space-y-3.5">
                @forelse(($stats['popularProducts'] ?? []) as $item)
                    @php
                        $width = min(100, max(12, (int) round(($item->total_sales / $popularMax) * 100)));
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs font-bold">
                            <p class="truncate pr-2 text-[#2f3d55]">{{ $item->product->name ?? 'Produk' }}</p>
                            <span class="text-[#63a2bb]">{{ number_format($item->total_sales) }} Sales</span>
                        </div>
                        <div class="h-2 rounded-full bg-[#f2f4f9]">
                            <div class="h-full rounded-full bg-[#63a2bb]" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm font-semibold text-[#95a0b5]">Belum ada data penjualan produk.</p>
                @endforelse
            </div>
        </article>

        <article class="pm-card p-3.5">
            <h2 class="text-[24px] font-extrabold leading-none text-[#24344b]">Gender Distribution</h2>
            <div class="mt-5 flex justify-center">
                <div class="relative flex h-36 w-36 items-center justify-center rounded-full" style="background: conic-gradient(#63a2bb 0deg {{ $womenPercent * 3.6 }}deg, #e8e8ea {{ $womenPercent * 3.6 }}deg 360deg);">
                    <div class="h-24 w-24 rounded-full bg-white"></div>
                    <div class="absolute text-center">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-[#a0a9b8]">Terbanyak</p>
                        <p class="text-xs font-extrabold text-[#4b5a72]">{{ $dominantPercent }}%</p>
                        <p class="text-[10px] font-bold text-[#8f98aa]">{{ $dominantLabel }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-sm font-bold">
                <div class="flex items-center justify-between text-[#374760]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#63a2bb]"></span>Cewe (Women)</span>
                    <span>{{ $womenPercent }}%</span>
                </div>
                <div class="flex items-center justify-between text-[#374760]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#d9d9dd]"></span>Cowo (Men)</span>
                    <span>{{ $menPercent }}%</span>
                </div>
            </div>
        </article>

        <article class="pm-card p-3.5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[24px] font-extrabold leading-none text-[#24344b]">{{ $calendarBaseDate->format('F Y') }}</h2>
                <div class="text-xs text-[#9099a9]"><i class="fa-solid fa-angle-left mr-3"></i><i class="fa-solid fa-angle-right"></i></div>
            </div>

            <form method="GET" action="{{ route('admin.produk.index') }}" class="mb-3 grid grid-cols-1 gap-2">
                <input type="hidden" name="search" value="{{ $search }}">
                <div class="grid grid-cols-3 gap-2">
                    <input type="date" name="calendar_date" value="{{ $calendarSelectedDate->toDateString() }}" class="rounded-md border border-[#e2e6ef] bg-white px-2 py-1.5 text-[11px] font-bold text-[#55647b] focus:outline-none">
                    <input type="month" name="calendar_month" value="{{ $calendarBaseDate->format('Y-m') }}" class="rounded-md border border-[#e2e6ef] bg-white px-2 py-1.5 text-[11px] font-bold text-[#55647b] focus:outline-none">
                    <select name="calendar_year" class="rounded-md border border-[#e2e6ef] bg-white px-2 py-1.5 text-[11px] font-bold text-[#55647b] focus:outline-none">
                        @foreach($calendarYears as $year)
                            <option value="{{ $year }}" {{ (int) $calendarYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[#63a2bb] px-2 py-1.5 text-[11px] font-bold text-white hover:brightness-95">Terapkan Kalender</button>
            </form>

            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold uppercase text-[#b2baca]">
                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
            </div>

            <div class="mt-2 grid grid-cols-7 gap-1 text-center text-xs font-bold text-[#56617a]">
                @foreach($calendarDays as $day)
                    @php
                        $isCurrentMonth = $day->month === $calendarBaseDate->month && $day->year === $calendarBaseDate->year;
                        $isActive = $day->isSameDay($calendarSelectedDate);
                        $isSpecial = in_array($day->day, [1, 10], true) && $isCurrentMonth;
                    @endphp
                    <div class="h-6 rounded-md pt-1 {{ $isActive ? 'bg-[#63a2bb] text-white' : '' }} {{ !$isCurrentMonth ? 'text-[#cfd4de]' : '' }} {{ $isSpecial && !$isActive ? 'bg-[#f4f6fb]' : '' }}">
                        {{ $day->day }}
                    </div>
                @endforeach
            </div>

            <div class="mt-3 flex items-center gap-6 text-[10px] font-bold uppercase">
                <span class="inline-flex items-center gap-1.5 text-[#7d8799]"><span class="h-2 w-2 rounded-full bg-[#28c66f]"></span>Stock Update</span>
                <span class="inline-flex items-center gap-1.5 text-[#7d8799]"><span class="h-2 w-2 rounded-full bg-[#63a2bb]"></span>Campaign</span>
            </div>
        </article>
    </div>

    <article class="pm-card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e9f0] p-3.5">
            <h2 class="text-[24px] font-extrabold leading-none text-[#24344b]">Daftar Produk Master</h2>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.produk.index') }}" class="flex items-center gap-2">
                    <label class="relative">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-2.5 top-2.5 text-[11px] text-[#b3bbca]"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Produk..." class="h-8 rounded-md border border-[#e3e7ef] bg-white pl-8 pr-3 text-xs font-semibold text-[#51607a] focus:outline-none">
                    </label>

                    <button type="submit" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-[#e3e7ef] bg-white px-3 text-xs font-bold text-[#66758f] hover:bg-[#f7f9fc]">
                        <i class="fa-solid fa-filter text-[10px]"></i>
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="pm-scrollbar pm-table-scroll overflow-x-auto">
            <table class="min-w-[1220px] w-full">
                <thead class="border-b border-[#edf0f6] bg-[#fcfdff] text-[10px] uppercase tracking-[0.08em] text-[#b2baca]">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Gambar</th>
                        <th class="px-3 py-2 text-left">Nama Produk</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-left">Supplier</th>
                        <th class="px-3 py-2 text-left">Gender</th>
                        <th class="px-3 py-2 text-left">Tipe Olahraga</th>
                        <th class="px-3 py-2 text-left">Harga</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf0f6] text-sm">
                    @forelse($products as $index => $item)
                        @php
                            $rowNo = ($products->firstItem() ?? 1) + $index;
                            $gender = $genderPool[$rowNo % count($genderPool)];
                            $sportType = $sportPool[$rowNo % count($sportPool)];
                            $available = $item->stock > 0;
                        @endphp
                        <tr class="hover:bg-[#fafbfd]">
                            <td class="px-3 py-2.5 text-[11px] font-bold text-[#90a0b8]">#PRD-{{ str_pad((string) $rowNo, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-3 py-2.5">
                                <div class="h-10 w-10 overflow-hidden rounded-md border border-[#e2e6ee] bg-[#14171c]">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-[#737f95]"><i class="fa-regular fa-image text-xs"></i></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <p class="font-extrabold text-[#2a3a52]">{{ $item->name }}</p>
                                <p class="truncate text-[11px] font-semibold text-[#97a2b5]">{{ \Illuminate\Support\Str::limit($item->description, 44) }}</p>
                            </td>
                            <td class="px-3 py-2.5 text-sm font-bold text-[#55647b]">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-sm font-bold text-[#55647b]">{{ $item->supplier->store_name ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-sm font-bold text-[#55647b]">{{ $gender }}</td>
                            <td class="px-3 py-2.5 text-sm font-bold text-[#55647b]">{{ $sportType }}</td>
                            <td class="px-3 py-2.5 text-sm font-extrabold text-[#23354d]">{{ $formatRupiah($item->price) }}</td>
                            <td class="px-3 py-2.5">
                                <span class="text-[11px] font-extrabold uppercase {{ $available ? 'text-[#08a65a]' : 'text-[#d12a34]' }}">{{ $available ? 'Tersedia' : 'Stok Habis' }}</span>
                            </td>
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.produk.show', $item->id) }}" class="text-[#70809a] hover:text-[#2f415f]" title="Lihat Detail"><i class="fa-regular fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-sm font-semibold text-[#95a0b5]">Belum ada data produk untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e9f0] bg-[#fbfcff] px-3.5 py-2.5">
            <p class="text-xs font-semibold text-[#96a0b2]">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items</p>

            <div class="flex items-center gap-1">
                @php
                    $currentPage = $products->currentPage();
                    $lastPage = $products->lastPage();
                @endphp
                <a href="{{ $products->previousPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ $products->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">Previous</a>

                @for($page = max(1, $currentPage - 1); $page <= min($lastPage, $currentPage + 1); $page++)
                    <a href="{{ $products->url($page) }}" class="min-w-7 rounded-md border px-2.5 py-1.5 text-center text-xs font-extrabold {{ $page === $currentPage ? 'border-[#63a2bb] bg-[#63a2bb] text-white' : 'border-[#e3e7ef] bg-white text-[#7b879b]' }}">{{ $page }}</a>
                @endfor

                <a href="{{ $products->nextPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ !$products->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">Next</a>
            </div>
        </div>
    </article>
</section>
@endsection
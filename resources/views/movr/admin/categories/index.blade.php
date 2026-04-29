@extends('movr.layouts.admin')

@section('content')
@php
    $calendarDate = \Carbon\Carbon::parse($selectedDate ?? now()->toDateString());
    $calendarMonthDate = ($calendarMonthDate ?? now())->copy()->startOfMonth();
    $calendarStart = $calendarMonthDate->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $calendarEnd = $calendarMonthDate->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $calendarDays = [];
    for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
        $calendarDays[] = $day->copy();
    }
    $yearOptions = range(now()->year - 5, now()->year + 1);
@endphp

<style>
    .cat-card {
        border: 1px solid #e5e8ef;
        border-radius: 12px;
        background: #fff;
    }

    .cat-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .cat-scroll::-webkit-scrollbar-thumb {
        background: #bdc4d1;
        border-radius: 99px;
    }
</style>

<section class="space-y-4 text-[#273650]">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-3 xl:grid-cols-[1.9fr_1fr]">
        <div class="space-y-3">
            <div class="grid gap-3 sm:grid-cols-3">
                <article class="cat-card p-3.5">
                    <p class="text-[11px] font-bold text-[#96a0b2]">Total Categories</p>
                    <div class="mt-2 flex items-end gap-2">
                        <p class="text-[38px] font-extrabold leading-none text-[#63a2bb]">{{ number_format($totalCategories) }}</p>
                        <span class="text-[10px] font-bold text-[#23b263]">+3 new</span>
                    </div>
                </article>

                <article class="cat-card p-3.5">
                    <p class="text-[11px] font-bold text-[#96a0b2]">Active Products</p>
                    <p class="mt-2 text-[38px] font-extrabold leading-none text-[#273650]">{{ number_format($activeProducts) }}</p>
                </article>

                <article class="cat-card p-3.5">
                    <p class="text-[11px] font-bold text-[#96a0b2]">Avg Margin</p>
                    <p class="mt-2 text-[38px] font-extrabold leading-none text-[#273650]">{{ number_format($avgMargin, 0) }}%</p>
                </article>
            </div>

            <article class="cat-card p-3.5">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-[24px] font-extrabold leading-none text-[#273650]">Category Hierarchy Distribution</h2>
                    <span class="text-xs font-bold text-[#63a2bb]"><i class="fa-solid fa-filter mr-1"></i>Filter View</span>
                </div>

                <div class="space-y-3">
                    @forelse($hierarchyRows as $row)
                        @php
                            $category = $row['category'];
                            $subs = $category->children;
                            $subTotal = max(1, $subs->count());
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-[11px] font-extrabold uppercase text-[#a0a9b8]">
                                <span>{{ $category->name }}</span>
                                <span>{{ $row['subcategories_count'] }} SUB-CATEGORIES</span>
                            </div>
                            <div class="grid grid-cols-12 gap-1 rounded-md bg-[#f3f5fa] p-1">
                                @if($subs->count() > 0)
                                    @foreach($subs as $sub)
                                        @php
                                            $width = max(2, (int) floor((12 / $subTotal)));
                                            $color = $loop->odd ? '#63a2bb' : '#8ec2d3';
                                        @endphp
                                        <div class="rounded-sm px-2 py-1 text-[10px] font-bold text-white" style="grid-column: span {{ $width }} / span {{ $width }}; background: {{ $color }};">
                                            {{ $sub->name }} ({{ max(1, (int) round(($sub->products_count / max(1, $category->products_count)) * 100)) }}%)
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-12 rounded-sm bg-[#d6dce8] px-2 py-1 text-[10px] font-bold text-[#63708a]">No sub-categories</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm font-semibold text-[#98a2b3]">Belum ada data hierarchy kategori.</p>
                    @endforelse
                </div>
            </article>
        </div>

        <article class="cat-card p-3.5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[24px] font-extrabold leading-none text-[#273650]">{{ $calendarMonthDate->format('F Y') }}</h2>
                <div class="text-[#98a2b3]"><i class="fa-solid fa-angle-left mr-4"></i><i class="fa-solid fa-angle-right"></i></div>
            </div>

            <form method="GET" action="{{ route('admin.kategori.index') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 gap-2">
                    <input type="date" name="calendar_date" value="{{ $calendarDate->toDateString() }}" class="rounded-md border border-[#e2e6ef] px-2.5 py-2 text-xs font-bold text-[#5a6780] focus:outline-none">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="month" name="calendar_month" value="{{ $calendarMonthDate->format('Y-m') }}" class="rounded-md border border-[#e2e6ef] px-2.5 py-2 text-xs font-bold text-[#5a6780] focus:outline-none">
                        <select name="calendar_year" class="rounded-md border border-[#e2e6ef] px-2.5 py-2 text-xs font-bold text-[#5a6780] focus:outline-none">
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full rounded-md bg-[#63a2bb] px-3 py-2 text-xs font-bold text-white hover:brightness-95">Terapkan Kalender</button>
            </form>

            <div class="mt-3 grid grid-cols-7 gap-1 text-center text-[10px] font-bold uppercase text-[#b4bccb]">
                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
            </div>

            <div class="mt-2 grid grid-cols-7 gap-1 text-center text-sm font-bold text-[#4d5d79]">
                @foreach($calendarDays as $day)
                    @php
                        $inMonth = $day->month === $calendarMonthDate->month && $day->year === $calendarMonthDate->year;
                        $isActive = $day->isSameDay($calendarDate);
                    @endphp
                    <div class="h-8 rounded-md pt-1.5 {{ $isActive ? 'bg-[#63a2bb] text-white' : '' }} {{ !$inMonth ? 'text-[#ccd2de]' : '' }}">
                        {{ $day->day }}
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-2 text-[11px] font-bold text-[#7c879a]">
                <p>Category Audit</p>
                <p class="text-[#4f5d75]">• New Launch: Kids Fall</p>
            </div>
        </article>
    </div>

    <div class="flex items-center justify-between">
        <h2 class="text-[24px] font-extrabold leading-none text-[#273650]">Favorite Categories</h2>
        <a href="{{ route('admin.kategori.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-[#63a2bb] px-3 py-2 text-xs font-bold text-white hover:brightness-95">
            <i class="fa-solid fa-plus text-[10px]"></i>
            Add Category
        </a>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @forelse($favoriteCategories as $fav)
            @php
                $cover = $fav['image'] ? asset('storage/' . $fav['image']) : null;
                $statusColor = $fav['status'] === 'Top Sale' ? 'text-[#20b567]' : ($fav['status'] === 'Stable' ? 'text-[#5379db]' : 'text-[#d29f2a]');
            @endphp
            <article class="cat-card overflow-hidden">
                <div class="h-28 bg-[#0f1725]">
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $fav['category']->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-[#8e99ac]"><i class="fa-regular fa-image text-2xl"></i></div>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="truncate text-sm font-extrabold text-[#22344b]">{{ $fav['category']->name }}</h3>
                    <div class="mt-2 flex items-center justify-between text-[11px] font-bold text-[#95a0b5]">
                        <span>INVENTORY</span>
                        <span>STATUS</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[12px] font-extrabold">
                        <span class="text-[#2f3d55]">{{ number_format($fav['inventory']) }} items</span>
                        <span class="{{ $statusColor }}">{{ $fav['status'] }}</span>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-sm font-semibold text-[#98a2b3]">Belum ada kategori favorit.</p>
        @endforelse
    </div>

    <article class="cat-card overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-[#e6e9f0] p-3.5">
            <h2 class="text-[24px] font-extrabold leading-none text-[#273650]">Master Category Explorer</h2>
            <label class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-[11px] text-[#b4bccb]"></i>
                <input type="text" placeholder="Filter categories..." class="h-8 rounded-md border border-[#e3e7ef] bg-white pl-8 pr-3 text-xs font-semibold text-[#5a6780] focus:outline-none">
            </label>
        </div>

        <div class="cat-scroll overflow-x-auto">
            <table class="min-w-[1080px] w-full">
                <thead class="border-b border-[#edf0f6] bg-[#fcfdff] text-[10px] uppercase tracking-[0.08em] text-[#b2baca]">
                    <tr>
                        <th class="px-4 py-2 text-left">Category Name</th>
                        <th class="px-4 py-2 text-left">Sub-Cats</th>
                        <th class="px-4 py-2 text-left">Products</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf0f6] text-sm">
                    @forelse($masterCategories as $category)
                        <tr class="hover:bg-[#fafbfd]">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-2">
                                    <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-md bg-[#e8f3f7] text-[10px] font-extrabold text-[#63a2bb]">{{ $loop->iteration }}</span>
                                    <div>
                                        <p class="font-extrabold text-[#293a54]">{{ $category->name }}</p>
                                        <p class="text-[11px] font-semibold text-[#9aa4b7]">Main Parent Category</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-bold text-[#51607b]">{{ $category->children_count }}</td>
                            <td class="px-4 py-3 font-bold text-[#51607b]">{{ $category->products_count }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-[#eaf9f0] px-2 py-0.5 text-[10px] font-extrabold text-[#20b567]">Active</span>
                            </td>
                            <td class="px-4 py-3">
                                <details>
                                    <summary class="cursor-pointer list-none text-[#8692a8] hover:text-[#344762]"><i class="fa-solid fa-chevron-down"></i></summary>
                                    <div class="mt-2 w-[340px] rounded-lg border border-[#e4e8ef] bg-[#fbfcff] p-2.5 shadow-sm">
                                        @forelse($category->children as $sub)
                                            <div class="mb-2 flex items-center justify-between rounded-md border border-[#edf0f6] bg-white px-2 py-1.5 last:mb-0">
                                                <div>
                                                    <p class="text-xs font-bold text-[#31435f]">{{ $sub->name }}</p>
                                                    <p class="text-[10px] font-semibold text-[#99a4b7]">{{ $sub->products_count }} products</p>
                                                </div>
                                                <a href="{{ route('admin.kategori.show', ['kategori' => $sub->id, 'back' => url()->full()]) }}" class="text-[10px] font-extrabold text-[#63a2bb] hover:underline">View All</a>
                                            </div>
                                        @empty
                                            <div class="rounded-md border border-[#edf0f6] bg-white px-2 py-1.5 text-xs font-semibold text-[#99a4b7]">No sub-categories available.</div>
                                        @endforelse
                                        <a href="{{ route('admin.kategori.show', ['kategori' => $category->id, 'back' => url()->full()]) }}" class="mt-2 block text-right text-[10px] font-extrabold text-[#24364f] hover:underline">View All Products</a>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm font-semibold text-[#95a0b5]">Belum ada kategori untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e9f0] bg-[#fbfcff] px-3.5 py-2.5">
            <p class="text-xs font-semibold text-[#96a0b2]">Showing {{ $masterCategories->firstItem() ?? 0 }}-{{ $masterCategories->lastItem() ?? 0 }} of {{ $masterCategories->total() }} categories</p>

            <div class="flex items-center gap-1">
                <a href="{{ $masterCategories->previousPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ $masterCategories->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">Prev</a>
                <span class="min-w-7 rounded-md border border-[#63a2bb] bg-[#63a2bb] px-2.5 py-1.5 text-center text-xs font-extrabold text-white">{{ $masterCategories->currentPage() }}</span>
                <a href="{{ $masterCategories->nextPageUrl() ?: '#' }}" class="rounded-md border border-[#e3e7ef] bg-white px-3 py-1.5 text-xs font-bold text-[#7b879b] {{ !$masterCategories->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">Next</a>
            </div>
        </div>
    </article>
</section>
@endsection
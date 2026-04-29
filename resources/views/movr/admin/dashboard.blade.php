@extends('movr.layouts.admin')

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $toCompact = function ($value) {
        $number = (float) $value;
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'm';
        }
        if ($number >= 1000) {
            return number_format($number / 1000, 1) . 'k';
        }

        return number_format($number, 0);
    };

    $avgOrderValue = $total_orders > 0 ? ($total_revenue / $total_orders) : 0;
    $selectedDate = request('date', now()->toDateString());
    $selectedMonth = request('month', now()->format('Y-m'));
    $selectedYear = (int) request('year', now()->year);
    $yearOptions = range(now()->year - 5, now()->year + 1);
    $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
    $pickedDate = \Carbon\Carbon::parse($selectedDate);
    if (!$pickedDate->isSameMonth($monthStart)) {
        $pickedDate = $monthStart->copy()->endOfMonth();
    }
    $rangeLabel = $monthStart->format('M d') . ' - ' . $pickedDate->format('M d');
    $chartSource = collect($daily_sales_chart ?? [])->values();
    $barSeries = collect(range(0, 9))->map(function ($i) use ($chartSource) {
        $index = min($chartSource->count() - 1, $i * 3);

        return $chartSource->get(max(0, $index), [
            'label' => 'M' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
            'total' => 0,
        ]);
    });

    $maxBarValue = max(1, (float) $barSeries->max('total'));
    $barHeights = $barSeries->map(function ($item) use ($maxBarValue) {
        return max(35, (int) round(($item['total'] / $maxBarValue) * 150));
    })->values();

    $activityRows = collect($notifications['new_orders'] ?? [])->take(2);
    $donutValue = max(0, $monthly_sales_amount ?? 0);
    $donutCenterLabel = $toCompact($donutValue);
@endphp

<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3 px-1 py-1">
        <div>
            <h1 class="text-[30px] font-extrabold leading-none text-[#22344b] md:text-[38px]">Dashboard Analytics</h1>
            <p class="mt-1 text-sm font-semibold text-[#9aa3b3]">Overview of your store's performance for {{ now()->format('F Y') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <details class="group relative">
                <summary class="flex min-h-10 cursor-pointer list-none items-center gap-2 rounded-lg border border-[#dbe1ea] bg-white px-3 py-2 text-sm font-bold text-[#4a5568] transition hover:bg-[#f7f8fc]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#667085]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path d="M8 2v4M16 2v4M3 10h18"/>
                    </svg>
                    <span>{{ $rangeLabel }}</span>
                </summary>

                <div class="absolute right-0 z-30 mt-2 w-[300px] rounded-xl border border-[#dbe1ea] bg-white p-3 shadow-lg">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-2.5">
                        <label class="block text-xs font-bold text-[#8591a4]">Tanggal
                            <input type="date" name="date" value="{{ $selectedDate }}" class="mt-1 w-full rounded-lg border border-[#dbe1ea] bg-white px-2.5 py-2 text-xs font-bold text-[#2d3f57] focus:outline-none">
                        </label>

                        <label class="block text-xs font-bold text-[#8591a4]">Bulan
                            <input type="month" name="month" value="{{ $selectedMonth }}" class="mt-1 w-full rounded-lg border border-[#dbe1ea] bg-white px-2.5 py-2 text-xs font-bold text-[#2d3f57] focus:outline-none">
                        </label>

                        <label class="block text-xs font-bold text-[#8591a4]">Tahun
                            <select name="year" class="mt-1 w-full rounded-lg border border-[#dbe1ea] bg-white px-2.5 py-2 text-xs font-bold text-[#2d3f57] focus:outline-none">
                                @foreach($yearOptions as $year)
                                    <option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </label>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-[#24364f] px-3 py-2 text-xs font-bold text-white hover:brightness-95">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </details>

            <a href="{{ route('admin.report') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#63a2bb] px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:brightness-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3v12"/>
                    <path d="m7 10 5 5 5-5"/>
                    <path d="M5 21h14"/>
                </svg>
                Export Report
            </a>
        </div>
    </div>

    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-3 flex items-start justify-between">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#eef4ff] text-[#4e7bdc]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="6" width="20" height="12" rx="2"/>
                        <path d="M6 12h.01M10 12h8"/>
                    </svg>
                </span>
                <span class="text-xs font-bold text-[#1a9f63]">+12.5%</span>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#96a0b2]">Total Revenue</p>
            <p class="mt-1 text-[24px] font-extrabold leading-none text-[#22344b]">{{ $formatRupiah($total_revenue) }}</p>
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-3 flex items-start justify-between">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#e8f3f7] text-[#2f7e97]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="4" width="14" height="16" rx="2"/>
                        <path d="M9 2v4M15 2v4M8 10h8"/>
                    </svg>
                </span>
                <span class="text-xs font-bold text-[#1a9f63]">+8.2%</span>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#96a0b2]">Total Orders</p>
            <p class="mt-1 text-[24px] font-extrabold leading-none text-[#22344b]">{{ number_format($total_orders) }}</p>
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-3 flex items-start justify-between">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#fff8ed] text-[#d18229]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </span>
                <span class="text-xs font-bold text-[#d16060]">-2.4%</span>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#96a0b2]">Active Customers</p>
            <p class="mt-1 text-[24px] font-extrabold leading-none text-[#22344b]">{{ number_format($total_customers) }}</p>
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-3 flex items-start justify-between">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#f4efff] text-[#8e63cc]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18"/>
                        <path d="m8 14 3-3 2 2 4-5"/>
                    </svg>
                </span>
                <span class="text-xs font-bold text-[#1a9f63]">+4.1%</span>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#96a0b2]">Avg. Order Value</p>
            <p class="mt-1 text-[24px] font-extrabold leading-none text-[#22344b]">{{ $formatRupiah($avgOrderValue) }}</p>
        </article>
    </div>

    <div class="grid gap-3 xl:grid-cols-[1.8fr_1fr]">
        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-[22px] font-extrabold leading-none text-[#22344b] md:text-[24px]">Performance Trends</h2>
                    <p class="text-sm font-semibold text-[#9aa3b3]">Daily revenue and traffic analysis</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold text-[#8f98aa]">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#63a2bb]"></span>Revenue</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#d6dbe5]"></span>Previous</span>
                </div>
            </div>

            <div class="grid h-[280px] grid-cols-10 items-end gap-2 rounded-xl border border-[#f0f2f7] bg-[#fcfdff] px-4 pb-5 pt-4">
                @foreach($barSeries as $index => $point)
                    @php
                        $isHighlight = $index === 4;
                        $height = $barHeights[$index] ?? 35;
                        $barColor = $isHighlight ? '#63a2bb' : ($index > 7 ? '#9dc9d8' : '#c5e0e9');
                    @endphp
                    <div class="flex flex-col items-center justify-end gap-2">
                        <div class="w-full rounded-t-md border-t-[3px] border-[#63a2bb]" style="height: {{ $height }}px; background-color: {{ $barColor }};"></div>
                        <span class="text-[10px] font-bold uppercase text-[#9aa3b3]">{{ strtoupper(substr($point['label'], 0, 3)) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <h2 class="text-[22px] font-extrabold leading-none text-[#22344b] md:text-[24px]">Sales Distribution</h2>

            <div class="mt-6 flex justify-center">
                <div class="relative flex h-48 w-48 items-center justify-center rounded-full" style="background: conic-gradient(#63a2bb 0deg 105deg, #8ec2d3 105deg 220deg, #e9eaee 220deg 360deg);">
                    <div class="absolute h-36 w-36 rounded-full bg-white"></div>
                    <div class="relative z-10 text-center">
                        <p class="text-[34px] font-extrabold leading-none text-[#3d819a]">{{ $donutCenterLabel }}</p>
                        <p class="mt-1 text-[12px] font-extrabold uppercase tracking-[0.08em] text-[#8f98aa]">Total Sales</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-3 text-sm font-bold">
                <div class="flex items-center justify-between text-[#445167]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#63a2bb]"></span>Electronics</span>
                    <span>{{ $formatRupiah(79420) }}</span>
                </div>
                <div class="flex items-center justify-between text-[#445167]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#8ec2d3]"></span>Fashion</span>
                    <span>{{ $formatRupiah(24110) }}</span>
                </div>
                <div class="flex items-center justify-between text-[#445167]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#c5e0e9]"></span>Home & Living</span>
                    <span>{{ $formatRupiah(18062) }}</span>
                </div>
            </div>
        </article>
    </div>

    <div class="grid gap-3 xl:grid-cols-[1.3fr_1fr]">
        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-[22px] font-extrabold leading-none text-[#22344b] md:text-[24px]">Monthly Revenue Comparison</h2>
                <span class="rounded-md border border-[#dbe1ea] bg-[#f7f8fc] px-2 py-1 text-[11px] font-bold text-[#75809a]">Year {{ now()->year }}</span>
            </div>

            <div class="mt-4 space-y-3">
                @foreach(($monthly_sales ?? []) as $month)
                    @php
                        $widthPercent = $maxBarValue > 0 ? min(100, max(6, (int) round(($month['total'] / $maxBarValue) * 100))) : 6;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs font-bold text-[#667085]">
                            <span>{{ $month['label'] }}</span>
                            <span>{{ $formatRupiah($month['total']) }}</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#eef1f7]">
                            <div class="h-full rounded-full {{ $month['is_current'] ? 'bg-[#63a2bb]' : 'bg-[#d4dbea]' }}" style="width: {{ $widthPercent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-xl border border-[#e4e8ef] bg-white p-4">
            <h2 class="text-[22px] font-extrabold leading-none text-[#22344b] md:text-[24px]">Recent Activities</h2>

            <div class="mt-4 space-y-3">
                @forelse($activityRows as $activity)
                    <div class="flex items-start justify-between gap-3 rounded-lg border border-[#edf1f8] bg-[#fbfcff] px-3 py-3">
                        <div class="flex items-start gap-2.5">
                            <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-[#e8f9f0] text-[#20a35f]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.7L23 6H6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#22344b]">New order received from {{ $activity->user->name ?? 'Customer' }}</p>
                                <p class="text-xs font-semibold text-[#98a2b3]">{{ $activity->created_at?->diffForHumans() ?? 'just now' }} - Order #ORD-{{ 8400 + (int) $activity->id }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-extrabold text-[#2f4767]">{{ $formatRupiah($activity->total_amount ?? 0) }}</p>
                    </div>
                @empty
                    <div class="rounded-lg border border-[#edf1f8] bg-[#fbfcff] px-3 py-4 text-sm font-semibold text-[#8d97a9]">Belum ada aktivitas terbaru.</div>
                @endforelse

                <div class="flex items-start justify-between gap-3 rounded-lg border border-[#edf1f8] bg-[#fbfcff] px-3 py-3">
                    <div class="flex items-start gap-2.5">
                        <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-[#edf0ff] text-[#6573d4]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <path d="M20 8v6M23 11h-6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#22344b]">New customer registered</p>
                            <p class="text-xs font-semibold text-[#98a2b3]">45 minutes ago - Mobile App</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-extrabold text-[#63a2bb] hover:underline">View Profile</a>
                </div>
            </div>
        </article>
    </div>
</section>
@endsection

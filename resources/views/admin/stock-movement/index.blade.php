@extends('layouts.admin')

@section('title', 'Stock Movement Log')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Stock Movement Log</h1>
            <p class="text-sm text-slate-500 mt-1">Log histori pergerakan stok produk.</p>
        </div>

        <a href="{{ route('admin.stock-movement.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            📥 Export Excel
        </a>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
            <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
            <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <select name="jenis" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Types</option>
                <option value="in" {{ ($jenis_filter ?? '') === 'in' ? 'selected' : '' }}>IN (Masuk)</option>
                <option value="out" {{ ($jenis_filter ?? '') === 'out' ? 'selected' : '' }}>OUT (Keluar)</option>
                <option value="adjustment" {{ ($jenis_filter ?? '') === 'adjustment' ? 'selected' : '' }}>ADJUSTMENT</option>
            </select>

            <select name="produk_id" class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Products</option>
                @foreach($produk_list as $p)
                    <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                        {{ $p->nama_produk }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Filter</button>
        </form>
    </div>

    {{-- Movements Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Produk / Variant</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Qty</th>
                        <th class="py-3 px-4">Stok Sebelum</th>
                        <th class="py-3 px-4">Stok Sesudah</th>
                        <th class="py-3 px-4">Referensi</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        @php
                            $jenis_badge = match($m->jenis) {
                                'in' => 'bg-green-50 text-green-700',
                                'out' => 'bg-red-50 text-red-700',
                                'adjustment' => 'bg-blue-50 text-blue-700',
                                default => 'bg-slate-50 text-slate-700'
                            };
                            $jenis_display = strtoupper($m->jenis);
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50 text-xs">
                            <td class="py-3 px-4">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium">{{ $m->detailProduk->produk->nama_produk ?? '-' }}</div>
                                <div class="text-slate-500">{{ $m->detailProduk->warna->nama_warna ?? '-' }} / {{ $m->detailProduk->ukuran ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">{{ $m->detailProduk->sku ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full font-semibold {{ $jenis_badge }}">
                                    {{ $jenis_display }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold">{{ $m->qty }}</td>
                            <td class="py-3 px-4">{{ $m->stok_sebelum }}</td>
                            <td class="py-3 px-4">{{ $m->stok_sesudah }}</td>
                            <td class="py-3 px-4">{{ $m->referensi }}</td>
                            <td class="py-3 px-4">{{ $m->catatan }}</td>
                            <td class="py-3 px-4">{{ $m->dibuat_oleh }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 px-4 text-center text-slate-600">
                                Belum ada log pergerakan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($movements ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

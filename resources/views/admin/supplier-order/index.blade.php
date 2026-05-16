@extends('layouts.admin')

@section('title', 'Supplier Order Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Supplier Order Management</h1>
                <p class="text-slate-600">Kelola purchase order (PO) ke supplier, track penerimaan stok.</p>
            </div>
            <a href="{{ route('admin.supplier-order.create') }}" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">+ Buat PO Baru</a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="space-y-3 sm:space-y-0 sm:flex gap-3 items-end flex-wrap">
            <select name="supplier_id" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Supplier</option>
                @foreach($supplier_list ?? [] as $supp)
                    <option value="{{ $supp->supplier_id }}" {{ ($supplier_filter ?? '') == $supp->supplier_id ? 'selected' : '' }}>
                        {{ $supp->nama_toko }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="draft" {{ ($status_filter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="dikirim" {{ ($status_filter ?? '') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="diterima" {{ ($status_filter ?? '') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="dibatalkan" {{ ($status_filter ?? '') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
            <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Filter</button>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs font-semibold text-slate-700 uppercase">
                        <th class="px-4 py-3">Kode PO</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Tanggal Order</th>
                        <th class="px-4 py-3">Total Item</th>
                        <th class="px-4 py-3">Total Harga</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dibuat Oleh</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $po)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 text-xs">
                            <td class="px-4 py-3 font-mono font-medium">{{ $po->kode_order }}</td>
                            <td class="px-4 py-3">{{ $po->supplier?->nama_toko ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $po->tanggal_order?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $po->total_item }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $colors = [
                                        'draft' => 'gray',
                                        'dikirim' => 'blue',
                                        'diterima' => 'green',
                                        'dibatalkan' => 'red',
                                    ];
                                    $color = $colors[$po->status] ?? 'gray';
                                @endphp
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-2 py-1 rounded-full font-semibold">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $po->admin?->pengguna?->nama_pengguna ?? '-' }}</td>
                            <td class="px-4 py-3 space-x-2">
                                <a href="{{ route('admin.supplier-order.show', $po->supplier_order_id) }}" class="text-blue-600 hover:underline">👁️</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 px-4 text-center text-slate-600">
                                Tidak ada PO.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($orders ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

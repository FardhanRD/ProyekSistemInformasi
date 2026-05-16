@extends('layouts.admin')

@section('title', 'Customer Order Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Customer Order Management</h1>
                <p class="text-slate-600">Kelola pesanan, verifikasi pembayaran, dan track pengiriman.</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="space-y-3 sm:space-y-0 sm:flex gap-3 items-end flex-wrap">
            <input type="text" name="search" value="{{ $search_filter ?? '' }}" placeholder="Cari No Pesanan atau Nama Pembeli..." class="flex-1 min-w-[200px] rounded-xl border border-slate-200 px-4 py-2 text-sm">
            
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pembayaran_menunggu" {{ ($status_filter ?? '') === 'pembayaran_menunggu' ? 'selected' : '' }}>Pembayaran Menunggu</option>
                <option value="pembayaran_dikonfirmasi" {{ ($status_filter ?? '') === 'pembayaran_dikonfirmasi' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                <option value="pesanan_diproses" {{ ($status_filter ?? '') === 'pesanan_diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="pesanan_dikirim" {{ ($status_filter ?? '') === 'pesanan_dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="pesanan_selesai" {{ ($status_filter ?? '') === 'pesanan_selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="pesanan_dibatalkan" {{ ($status_filter ?? '') === 'pesanan_dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
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
                        <th class="px-4 py-3">No Pesanan</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status Pembayaran</th>
                        <th class="px-4 py-3">Status Pesanan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 text-xs">
                            <td class="px-4 py-3 font-mono font-medium">{{ $order->kode_transaksi }}</td>
                            <td class="px-4 py-3">{{ $order->pengguna?->nama_pengguna ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $order->tanggal?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $status_pembayaran = $order->pembayaran?->status_pembayaran ?? 'belum_ada';
                                    $colors = [
                                        'menunggu_konfirmasi' => 'yellow',
                                        'berhasil' => 'green',
                                        'gagal' => 'red',
                                        'ditolak' => 'red',
                                        'belum_ada' => 'gray',
                                    ];
                                    $color = $colors[$status_pembayaran] ?? 'gray';
                                @endphp
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-2 py-1 rounded-full font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $status_pembayaran)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $colors = [
                                        'pembayaran_menunggu' => 'yellow',
                                        'pembayaran_dikonfirmasi' => 'blue',
                                        'pesanan_diproses' => 'purple',
                                        'pesanan_dikirim' => 'indigo',
                                        'pesanan_selesai' => 'green',
                                        'pesanan_dibatalkan' => 'red',
                                    ];
                                    $color = $colors[$order->status] ?? 'gray';
                                @endphp
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-2 py-1 rounded-full font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 space-x-2">
                                <a href="{{ route('admin.customer-order.show', $order->transaksi_id) }}" class="text-blue-600 hover:underline">👁️</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 px-4 text-center text-slate-600">
                                Tidak ada pesanan.
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

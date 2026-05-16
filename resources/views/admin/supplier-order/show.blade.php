@extends('layouts.admin')

@section('title', 'Detail Supplier Order')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $po->kode_order }}</h1>
                <p class="text-slate-600 text-sm">{{ $po->tanggal_order?->format('Y-m-d H:i') }} | {{ $po->supplier?->nama_toko }}</p>
            </div>
            <div class="flex gap-2">
                @if($po->status === 'draft')
                    <a href="{{ route('admin.supplier-order.index') }}" class="rounded-xl border border-slate-200 text-slate-900 px-4 py-2 font-semibold hover:bg-slate-50">← Kembali</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Card --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Status</p>
            @php
                $colors = [
                    'draft' => 'gray',
                    'dikirim' => 'blue',
                    'diterima' => 'green',
                    'dibatalkan' => 'red',
                ];
                $color = $colors[$po->status] ?? 'gray';
            @endphp
            <p class="text-lg font-bold text-{{ $color }}-600">{{ ucfirst($po->status) }}</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Total Item</p>
            <p class="text-lg font-bold">{{ $po->total_item }}</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Total Harga</p>
            <p class="text-lg font-bold text-[#2B9BAF]">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Supplier Info --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Informasi Supplier</h2>
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-600 font-semibold mb-1">Nama Toko</p>
                <p class="text-slate-900">{{ $po->supplier?->nama_toko ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">Pemilik</p>
                <p class="text-slate-900">{{ $po->supplier?->pemilik ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">Email</p>
                <p class="text-slate-900">{{ $po->supplier?->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">No Telepon</p>
                <p class="text-slate-900">{{ $po->supplier?->no_telepon ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Detail Item</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200">
                    <tr class="text-left font-semibold text-slate-600 uppercase text-xs">
                        <th class="pb-3">Produk</th>
                        <th class="pb-3 text-right">Qty</th>
                        <th class="pb-3 text-right">Harga Beli</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($po->details ?? [] as $detail)
                        <tr class="border-t border-slate-100">
                            <td class="py-3">
                                <div>
                                    <p class="font-medium">{{ $detail->detailProduk?->produk?->nama_produk ?? '-' }}</p>
                                    <p class="text-xs text-slate-600">{{ $detail->detailProduk?->warna?->nama_warna ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="py-3 text-right font-semibold">{{ $detail->qty }}</td>
                            <td class="py-3 text-right">Rp {{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 text-right font-semibold text-[#2B9BAF]">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-600">Tidak ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Catatan --}}
    @if($po->catatan)
        <div class="rounded-3xl border border-slate-200 bg-blue-50 p-5">
            <h2 class="font-semibold text-slate-900 mb-2">Catatan</h2>
            <p class="text-slate-700 text-sm">{{ $po->catatan }}</p>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3 justify-end">
        <a href="{{ route('admin.supplier-order.index') }}" class="rounded-xl border border-slate-200 text-slate-900 px-6 py-3 font-semibold hover:bg-slate-50">← Kembali</a>

        @if($po->status === 'draft' || $po->status === 'dikirim')
            <form method="POST" action="{{ route('admin.supplier-order.receive', $po->supplier_order_id) }}" style="display:inline;" onsubmit="return confirm('Tandai PO ini sudah diterima?')">
                @csrf
                <button type="submit" class="rounded-xl bg-green-500 text-white px-6 py-3 font-semibold hover:bg-green-600">Tandai Diterima</button>
            </form>
        @endif


        <a href="{{ route('admin.supplier-order.invoice-pdf', $po->supplier_order_id) }}" class="rounded-xl bg-slate-900 text-white px-6 py-3 font-semibold hover:bg-slate-800">📄 Invoice</a>
    </div>
</div>
@endsection

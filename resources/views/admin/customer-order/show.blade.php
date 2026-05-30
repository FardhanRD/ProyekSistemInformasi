@extends('layouts.admin')

@section('title', 'Detail Customer Order')

@section('content')
<div x-data="{ showProofModal: false, proofUrl: '', proofTitle: '' }" class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $order->kode_transaksi }}</h1>
                    <p class="text-slate-600 text-sm">{{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('Y-m-d H:i') }} | {{ $order->pengguna?->nama_pengguna }}</p>
            </div>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Status Pembayaran</p>
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
            <p class="text-lg font-bold text-{{ $color }}-600">{{ ucfirst(str_replace('_', ' ', $status_pembayaran)) }}</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Status Pesanan</p>
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
            <p class="text-lg font-bold text-{{ $color }}-600">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-slate-600 text-xs font-semibold mb-1">Total Pesanan</p>
            <p class="text-lg font-bold text-[#2B9BAF]">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Customer Info --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Data Pembeli</h2>
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-600 font-semibold mb-1">Nama</p>
                <p class="text-slate-900">{{ $order->pengguna?->nama_pengguna ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">Email</p>
                <p class="text-slate-900">{{ $order->pengguna?->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">No Telepon</p>
                <p class="text-slate-900">{{ $order->pengguna?->no_telepon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-600 font-semibold mb-1">Alamat Pengiriman</p>
                <p class="text-slate-900">{{ $order->alamat?->alamat_lengkap ?? '-' }}</p>
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
                        <th class="pb-3 text-right">Harga</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->details ?? [] as $item)
                        <tr class="border-t border-slate-100">
                            <td class="py-3">
                                <div>
                                    <p class="font-medium">{{ $item->produk?->nama_produk ?? '-' }}</p>
                                    <p class="text-xs text-slate-600">{{ $item->warna?->nama_warna ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="py-3 text-right">{{ $item->qty }}</td>
                            <td class="py-3 text-right">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 text-right font-semibold">Rp {{ number_format(($item->qty ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
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

    {{-- Payment Info --}}
    @if($order->pembayaran)
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Informasi Pembayaran</h2>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-600 font-semibold mb-1">Metode</p>
                    <p class="text-slate-900">{{ $order->pembayaran?->metode?->nama_metode ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-600 font-semibold mb-1">Tanggal Pembayaran</p>
                    <p class="text-slate-900">{{ $order->pembayaran?->tanggal_pembayaran?->format('Y-m-d H:i') ?? 'Belum dibayar' }}</p>
                </div>
                <div>
                    <p class="text-slate-600 font-semibold mb-1">Status</p>
                    @php
                        $status_badge = [
                            'menunggu_konfirmasi' => 'bg-amber-100 text-amber-700',
                            'berhasil' => 'bg-green-100 text-green-700',
                            'gagal' => 'bg-red-100 text-red-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                        ];
                        $badge_class = $status_badge[$order->pembayaran?->status_pembayaran] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $badge_class }}">
                        {{ ucfirst(str_replace('_', ' ', $order->pembayaran?->status_pembayaran ?? '-')) }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-600 font-semibold mb-1">Bukti Pembayaran</p>
                    @if($order->pembayaran?->bukti_pembayaran)
                        <button type="button"
                                @click="proofUrl='{{ Storage::url($order->pembayaran->bukti_pembayaran) }}'; proofTitle='{{ $order->kode_transaksi }}'; showProofModal = true"
                                class="text-[#63A2BB] font-semibold hover:underline">
                            📷 Lihat Bukti
                        </button>
                    @else
                        <p class="text-slate-900">-</p>
                    @endif
                </div>
            </div>

            @if($order->pembayaran && $order->pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                <form method="POST" action="{{ route('admin.customer-order.verify-payment', $order->pembayaran->pembayaran_id) }}" class="mt-4 flex">
                    @csrf
                    <button type="submit" class="rounded-xl bg-green-500 text-white px-6 py-2 font-semibold hover:bg-green-600">✓ Verifikasi Pembayaran</button>
                </form>
            @endif
        </div>
    @endif

    {{-- Tracking --}}
    @if(!$pesanan)
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Tracking Pesanan</h2>
            <div class="p-6 text-center rounded-2xl border border-dashed border-slate-200 bg-slate-50">
                <p class="text-slate-600 text-sm">Pesanan belum diproses oleh sistem logistik. Update tracking akan muncul setelah pesanan diambil oleh kurir.</p>
            </div>
        </div>
    @elseif(!$pesanan->trackingLogs || $pesanan->trackingLogs->isEmpty())
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Tracking Pesanan</h2>
            <div class="p-6 text-center rounded-2xl border border-dashed border-slate-200 bg-slate-50">
                <p class="text-slate-600 text-sm">Belum ada update pengiriman untuk pesanan ini. Mohon tunggu update dari kurir.</p>
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Tracking Pesanan</h2>
            <div class="space-y-2">
                @foreach($pesanan->trackingLogs as $log)
                    <div class="flex gap-4">
                        <div class="text-xs font-semibold text-slate-600 min-w-[120px]">{{ $log->waktu?->format('Y-m-d H:i') }}</div>
                        <div>
                            <p class="font-medium text-slate-900">{{ $log->status }}</p>
                            <p class="text-xs text-slate-600">{{ $log->catatan }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div x-show="showProofModal" x-cloak
         @click.self="showProofModal = false"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between bg-[#63A2BB] px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-white">Bukti Pembayaran</h3>
                    <p class="mt-0.5 text-xs text-white/70" x-text="proofTitle"></p>
                </div>
                <button type="button" @click="showProofModal = false" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white transition hover:bg-white/30">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="bg-slate-50 p-4">
                <div class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-3">
                    <img :src="proofUrl" alt="Bukti Pembayaran" class="max-h-[75vh] w-full rounded-2xl object-contain">
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 justify-end">
        <a href="{{ route('admin.customer-order.index') }}" class="rounded-xl border border-slate-200 text-slate-900 px-6 py-3 font-semibold hover:bg-slate-50">← Kembali</a>
        <a href="{{ route('admin.customer-order.invoice-pdf', $order->transaksi_id) }}" class="rounded-xl bg-slate-900 text-white px-6 py-3 font-semibold hover:bg-slate-800">📄 Invoice</a>
    </div>
</div>
@endsection

{{--
  // ── FILE: resources/views/order/tracking.blade.php ──
  Tracking page timeline dari tracking_log.
--}}

@extends('layouts.buyer')

@section('title','MOVR | Tracking')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold text-cyan-300">TRACKING</div>
        <h1 class="text-2xl md:text-3xl font-black">Lacak Paket</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 rounded-3xl border border-white/10 bg-white/5 p-5">
            <h2 class="font-bold text-lg">Informasi Pesanan</h2>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-300">No Resi</span><span class="font-semibold">{{ $order->no_resi ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-300">Ekspedisi</span><span class="font-semibold">{{ $order->ekspedisi->nama_ekspedisi ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-300">Estimasi Tiba</span><span class="font-semibold">{{ $order->estimasi_tiba ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-300">Status</span><span class="font-semibold">{{ $order->status_pesanan ?? '-' }}</span></div>
            </div>

            @if($order->alamat_pengiriman)
                <div class="mt-4 text-sm text-slate-300">
                    <div class="font-semibold text-white mb-2">Alamat</div>
                    <div>{{ $order->alamat_pengiriman }}</div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-7 rounded-3xl border border-white/10 bg-white/5 p-5">
            <h2 class="font-bold text-lg">Timeline</h2>

            @if(empty($logs) || $logs->isEmpty())
                <div class="mt-4 text-slate-300 text-sm">Belum ada log tracking.</div>
            @else
                <div class="mt-4 space-y-4">
                    @foreach($logs as $log)
                        <div class="flex gap-4">
                            <div class="mt-1 flex flex-col items-center">
                                <div class="w-4 h-4 rounded-full bg-cyan-400 shadow-[0_0_0_5px_rgba(56,189,248,.15)]"></div>
                                <div class="w-px flex-1 bg-white/10 mt-1"></div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-bold">{{ $log->status }}</div>
                                        <div class="text-slate-300 text-sm mt-1">{{ $log->deskripsi }}</div>
                                        <div class="text-xs text-slate-400 mt-2">Lokasi: {{ $log->lokasi ?? '-' }}</div>
                                    </div>
                                    <div class="text-xs text-slate-400 whitespace-nowrap">{{ $log->waktu_update?->format('d M Y, H:i') ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


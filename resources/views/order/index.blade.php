{{--
  // ── FILE: resources/views/order/index.blade.php ──
  Order page with tabs and rating/review buttons.
--}}

@extends('layouts.app')

@section('title','MOVR | Pesanan')

@section('content')
<div class="space-y-6" x-data="orderTabs()">
    <div>
        <div class="text-xs font-semibold text-cyan-300">ORDER</div>
        <h1 class="text-2xl md:text-3xl font-black">Pesanan Saya</h1>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-3">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='semua'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('semua')">Semua</button>
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='menunggu_pembayaran'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('menunggu_pembayaran')">Menunggu Pembayaran</button>
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='diproses'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('diproses')">Diproses</button>
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='dikirim'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('dikirim')">Dikirim</button>
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='selesai'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('selesai')">Selesai</button>
            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-black/20 text-sm font-bold" :class="tab==='dibatalkan'?'bg-cyan-500 text-slate-950 border-cyan-400':'bg-black/20 text-white'" @click="setTab('dibatalkan')">Dibatalkan</button>
        </div>
    </div>

    @if(empty($orders) || $orders->isEmpty())
        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center text-slate-300">
            Belum ada pesanan.
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $o)
                <div x-show="tab==='semua' || tab===String('{{ $o->status }}')" x-cloak class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm text-slate-300">{{ $o->kode_transaksi ?? '' }}</div>
                            <div class="font-black text-lg">Total Rp {{ number_format((int)($o->total_harga ?? 0),0,',','.') }}</div>
                            <div class="text-xs text-slate-400 mt-1">Tanggal: {{ $o->tanggal?->format('d M Y') ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex rounded-full border border-white/10 px-3 py-1 text-xs font-bold" :class="statusBadge('{{ $o->status }}')">
                                {{ $o->status }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-9">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($o->details as $d)
                                    @php
                                        $img = $d->detailProduk?->gambarProduk()?->where('urutan',0)?->first();
                                    @endphp
                                    <div class="rounded-2xl border border-white/10 bg-black/20 p-3">
                                        <div class="h-20 w-full rounded-xl overflow-hidden border border-white/10 bg-white/5">
                                            @if($img?->url_gambar)
                                                <img src="{{ $img->url_gambar }}" alt="{{ $d->nama_produk_snap }}" class="w-full h-full object-cover" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-500">IMG</div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-200 mt-2 line-clamp-2">{{ $d->nama_produk_snap ?? '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <div class="space-y-2">
                                <a href="{{ route('order.tracking', $o->transaksi_id) }}" class="block rounded-2xl bg-white/5 border border-white/10 px-4 py-2 text-center text-sm font-bold hover:bg-white/10" x-show="['dikirim','selesai'].includes('{{ $o->status }}')">Lacak Paket</a>
                                <button type="button" class="block w-full rounded-2xl bg-cyan-500 px-4 py-2 text-center text-sm font-bold text-slate-950 hover:bg-cyan-400" x-show="'selesai'==='{{ $o->status }}'">Beri Ulasan</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function orderTabs(){
        return {
            tab: 'semua',
            setTab(t){ this.tab = t; },
            statusBadge(status){
                if(status==='menunggu_pembayaran') return 'bg-cyan-500/10 border-cyan-400/40 text-cyan-200';
                if(status==='diproses') return 'bg-amber-500/10 border-amber-400/40 text-amber-200';
                if(status==='dikirim') return 'bg-indigo-500/10 border-indigo-400/40 text-indigo-200';
                if(status==='selesai') return 'bg-emerald-500/10 border-emerald-400/40 text-emerald-200';
                if(status==='dibatalkan') return 'bg-rose-500/10 border-rose-400/40 text-rose-200';
                return 'bg-white/5 border-white/10 text-white/80';
            }
        }
    }
</script>
@endsection


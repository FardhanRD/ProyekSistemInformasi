{{--
  // ── FILE: resources/views/payment/show.blade.php ──
  Payment instruction + expired countdown + upload bukti bayar.
--}}

@extends('layouts.app')

@section('title','MOVR | Pembayaran')

@section('content')
<div class="space-y-6">

    <div>
        <div class="text-xs font-semibold text-cyan-300">PEMBAYARAN</div>
        <h1 class="text-2xl md:text-3xl font-black">Konfirmasi Pembayaran</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-7 rounded-3xl border border-white/10 bg-white/5 p-6">
            <div class="text-sm text-slate-300">Kode Transaksi</div>
            <div class="text-2xl font-black">{{ $trans->kode_transaksi ?? '-' }}</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                    <div class="text-xs text-slate-400">Total</div>
                    <div class="text-xl font-black">Rp {{ number_format((int)($trans->total_harga ?? 0),0,',','.') }}</div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-black/20 p-4" x-data="countdown('{{ $trans->expired_at ?? now()->addMinutes(30) }}')">
                    <div class="text-xs text-slate-400">Expired</div>
                    <div class="text-xl font-bold text-rose-300" x-text="time"></div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="font-bold text-lg">Instruksi</h2>
                <div class="mt-3 text-sm text-slate-300">
                    {{-- Ambil instruksi dari metode_pembayaran jika tersedia --}}
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="font-semibold">Metode yang tersedia</div>
                        <div class="mt-2">
                            @foreach($metodes as $m)
                                <div class="flex items-center justify-between gap-3 py-2 border-b border-white/10 last:border-b-0">
                                    <div class="flex items-center gap-3">
                                        @if($m->logo_url)
                                            <img src="{{ $m->logo_url }}" class="w-8 h-8 rounded-xl object-cover" />
                                        @endif
                                        <div>
                                            <div class="font-semibold">{{ $m->metode }}</div>
                                            <div class="text-xs text-slate-400">{{ $m->jenis }}</div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-300">Pilih</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 rounded-3xl border border-white/10 bg-white/5 p-6">
            <h2 class="font-bold text-lg">Upload Bukti</h2>

            @php
                $existingMethod = null;
            @endphp

            <form method="post" action="{{ route('payment.upload', ['transaksi_id' => $trans->transaksi_id]) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-semibold">Metode Pembayaran</label>
                    <select name="metode_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400">
                        <option value="" disabled selected>Pilih metode</option>
                        @foreach($metodes as $m)
                            <option value="{{ $m->metode_id }}">{{ $m->metode }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" accept="image/*" class="mt-2 w-full text-sm text-slate-300" />
                </div>

                <button type="submit" class="w-full rounded-3xl bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">Kirim Bukti</button>

                <div class="text-xs text-slate-400">
                    Setelah dikirim, admin akan mengonfirmasi pembayaran.
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function countdown(expiredAt) {
        return {
            time: '',
            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
            },
            tick() {
                const end = new Date(expiredAt);
                const now = new Date();
                const diff = end - now;
                if (diff <= 0) {
                    this.time = 'Expired';
                    return;
                }
                const s = Math.floor(diff / 1000);
                const m = Math.floor(s / 60);
                const h = Math.floor(m / 60);
                const d = Math.floor(h / 24);
                const hh = h % 24;
                const mm = m % 60;
                const ss = s % 60;
                this.time = `${d}d ${String(hh).padStart(2,'0')}:${String(mm).padStart(2,'0')}:${String(ss).padStart(2,'0')}`;
            }
        }
    }
</script>

@endsection


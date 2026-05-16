{{--
  // ── FILE: resources/views/cart/index.blade.php ──
  Keranjang view: list item + qty counter (+/-) AJAX (PATCH/DELETE) + summary.
--}}

@extends('layouts.buyer')

@section('title','MOVR | Keranjang')

@section('content')
<div class="space-y-6">
    <div class="flex items-end justify-between gap-3">
        <div>
            <div class="text-xs font-semibold text-cyan-300">KERANJANG</div>
            <h1 class="text-2xl md:text-3xl font-black">Keranjang Belanja</h1>
        </div>
    </div>

    @if(empty($items) || $items->isEmpty())
        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
            <div class="text-6xl">🛒</div>
            <div class="mt-3 text-lg font-bold">Keranjang kamu masih kosong</div>
            <div class="text-slate-300 text-sm mt-2">Mulai cari produk favoritmu di MOVR.</div>
            <a href="{{ route('home') }}" class="mt-5 inline-flex rounded-full bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">Belanja Sekarang</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 rounded-3xl border border-white/10 bg-white/5 p-4">
                <div class="space-y-4">
                    @foreach($items as $it)
                        @php
                            $detail = $it->detail ?? $it->detailProduk ?? null;
                            $produk = $detail?->produk ?? null;
                            $gambar = $produk?->gambarProduk()?->where('urutan',0)?->first();
                            $harga = $detail->harga ?? 0;
                            $stok = $detail->stok ?? 0;
                            $keranjangId = $it->keranjang_id ?? $it->id;
                        @endphp

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="flex gap-4">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                                    @if($gambar?->url_gambar)
                                        <img src="{{ $gambar->url_gambar }}" alt="{{ $produk?->nama_produk }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-500">N/A</div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <div class="font-bold">{{ $produk->nama_produk ?? '-' }}</div>
                                    <div class="text-sm text-slate-300 mt-1">Warna: {{ $detail->warna?->nama_warna ?? '-' }} • Ukuran: {{ $detail->ukuran ?? '-' }}</div>

                                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div>
                                            <div class="text-xs text-slate-300">Harga</div>
                                            <div class="font-black">Rp {{ number_format((int)$harga,0,',','.') }}</div>
                                        </div>

                                        <div x-data="cartQty('{{ $keranjangId }}', {{ (int)$stok }}, {{ (int)($it->jumlah ?? 1) }}, {{ (int)$harga }})" class="flex items-center gap-3">
                                            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10" @click="dec" :disabled="loading">-</button>
                                            <input type="number" class="w-16 text-center bg-transparent outline-none" x-model.number="qty" @input="onManualInput" :max="stok" min="1" :disabled="loading" />
                                            <button type="button" class="px-4 py-2 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10" @click="inc" :disabled="loading">+</button>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-xs text-slate-300">Subtotal</div>
                                            <div class="font-black" x-text="formatRupiah(subtotal)">Rp 0</div>
                                        </div>

                                        <div>
                                            <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 hover:bg-rose-500/20" @click="remove" :disabled="loading">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-4 rounded-3xl border border-white/10 bg-white/5 p-4">
                <h2 class="font-bold text-lg">Ringkasan</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @php
                        $subtotalAll = 0;
                        foreach($items as $it){
                            $detail = $it->detail ?? null;
                            $harga = $detail?->harga ?? 0;
                            $subtotalAll += $harga * (int)($it->jumlah ?? 1);
                        }
                    @endphp
                    <div class="flex justify-between"><span class="text-slate-300">Subtotal</span><span class="font-bold">Rp {{ number_format($subtotalAll,0,',','.') }}</span></div>
                </div>

                <a href="{{ route('checkout.index') }}" class="mt-6 block rounded-3xl bg-cyan-500 px-6 py-3 text-center text-sm font-bold text-slate-950 hover:bg-cyan-400">Checkout</a>
            </div>
        </div>
    @endif
</div>

<script>
    function cartQty(keranjangId, stok, initialQty, harga) {
        return {
            loading: false,
            qty: initialQty,
            stok: stok,
            harga: harga,
            get subtotal() {
                return this.qty * this.harga;
            },
            clamp() {
                if (this.qty < 1) this.qty = 1;
                if (this.stok && this.qty > this.stok) this.qty = this.stok;
            },
            formatRupiah(v) {
                return 'Rp ' + Number(v).toLocaleString('id-ID');
            },
            async sync(method, url) {
                this.loading = true;
                this.clamp();
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const resp = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({ jumlah: this.qty })
                    });
                    if (!resp.ok) throw new Error('Gagal');
                } finally {
                    this.loading = false;
                }
            },
            inc() {
                this.qty = this.qty + 1;
                this.clamp();
                this.sync('PATCH', '{{ url('/keranjang') }}/' + keranjangId);
            },
            dec() {
                this.qty = this.qty - 1;
                this.clamp();
                this.sync('PATCH', '{{ url('/keranjang') }}/' + keranjangId);
            },
            onManualInput() {
                this.clamp();
                // debounce sederhana
                clearTimeout(this._t);
                this._t = setTimeout(() => {
                    this.sync('PATCH', '{{ url('/keranjang') }}/' + keranjangId);
                }, 400);
            },
            async remove() {
                if (!confirm('Hapus item dari keranjang?')) return;
                this.loading = true;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const resp = await fetch('{{ url('/keranjang') }}/' + keranjangId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });
                    if (!resp.ok) throw new Error('Gagal');
                    window.location.reload();
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection


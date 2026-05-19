@extends('layouts.buyer')

@section('title', 'Checkout — MOVR')

@section('content')
@php
    $cartItems = collect($cart ?? collect());
    $metodesFlat = collect($metodes ?? collect());
    if ($metodesFlat->isNotEmpty() && $metodesFlat->first() instanceof \Illuminate\Support\Collection) {
        $metodesFlat = $metodesFlat->flatten(1);
    }

    $ekspedisiFirst = $ekspedisis->first();
    $alamatUtama = $addresses->firstWhere('is_utama', 1) ?? $addresses->first();
    $metodeDefault = $metodesFlat->first();

    $shippingMap = $ekspedisis->mapWithKeys(fn ($e) => [$e->ekspedisi_id => (float) ($e->ongkir_flat ?? 0)])->toArray();
    $subtotalServer = (float) ($subtotalProduk ?? 0);
    $ongkirServer = (float) ($shippingMap[$ekspedisiFirst->ekspedisi_id ?? 0] ?? 0);
    $biayaLayananServer = 1000;
    $voucherDiscountServer = (float) (session('applied_voucher_discount') ?? 0);
    $checkoutState = [
        'subtotalProduk' => $subtotalServer,
        'shippingMap' => $shippingMap,
        'selectedEkspedisi' => (int) ($ekspedisiFirst->ekspedisi_id ?? 0),
        'selectedMetode' => (int) ($metodeDefault->metode_id ?? 0),
        'selectedAddress' => (int) ($alamatUtama->alamat_id ?? 0),
        'voucherId' => (int) (session('applied_voucher_id') ?? ($voucher->voucher_id ?? 0)),
        'voucherDiscount' => $voucherDiscountServer,
    ];
@endphp

<div class="section-shell py-8 sm:py-10" x-data='checkoutPage(@json($checkoutState))'>
    <div class="mb-6 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('cart.index.alias') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition-all duration-200 hover:scale-105 hover:border-[#63A2BB] hover:text-[#63A2BB] hover:shadow-lg hover:shadow-[#63A2BB]/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#63A2BB]">Checkout</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-900">Selesaikan Pembelian</h1>
                    <p class="mt-1 text-sm text-slate-500">Lengkapi data pengiriman, layanan, dan pembayaran.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-500">
                <span class="rounded-full bg-[#63A2BB]/10 px-3 py-1 text-[#63A2BB]">1. Keranjang</span>
                <span class="rounded-full bg-[#63A2BB] px-3 py-1 text-white">2. Checkout</span>
                <span class="rounded-full bg-slate-100 px-3 py-1">3. Pembayaran</span>
            </div>
        </div>
    </div>

    @if($cartItems->isEmpty())
        <div class="card-surface p-10 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#63A2BB]/10 text-[#63A2BB]">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.7 3.4A1 1 0 007.2 18h9.6M7 13h10m0 0l1.2 6M7.2 18a1.8 1.8 0 103.6 0m6 0a1.8 1.8 0 103.6 0"/>
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-black text-slate-900">Item checkout belum dipilih</h2>
            <p class="mt-2 text-sm text-slate-500">Silakan kembali ke keranjang untuk memilih produk yang akan dibayar.</p>
            <a href="{{ route('cart.index.alias') }}" class="btn-primary mt-6 inline-flex items-center justify-center px-6 py-3">Kembali ke Keranjang</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8 space-y-6">
                <div class="card-surface p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-sm font-black text-[#63A2BB]">A</div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Informasi Penerima</h2>
                            <p class="text-sm text-slate-500">Pastikan data penerima sudah benar.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-[#F8FAFB] p-4 ring-1 ring-slate-200/70">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Lengkap</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ auth()->user()->nama_pengguna ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#F8FAFB] p-4 ring-1 ring-slate-200/70">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">No. Telepon</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ auth()->user()->no_telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-surface p-6">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-sm font-black text-[#63A2BB]">B</div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">Alamat Pengiriman</h2>
                                <p class="text-sm text-slate-500">Pilih alamat yang akan digunakan untuk pengiriman.</p>
                            </div>
                        </div>
                        <a href="{{ route('profile.address.create.alias') }}?return=checkout" class="inline-flex items-center gap-1 rounded-full bg-[#63A2BB]/10 px-4 py-2 text-sm font-semibold text-[#63A2BB] transition-all duration-200 hover:scale-105 hover:bg-[#63A2BB] hover:text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($addresses as $address)
                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition-all duration-200" x-bind:class="selectedAddress === {{ $address->alamat_id }} ? 'border-[#63A2BB] bg-[#63A2BB]/5 shadow-sm shadow-[#63A2BB]/10' : 'border-slate-200 bg-white hover:border-[#63A2BB]/40'">
                                <input type="radio" name="alamat_id" value="{{ $address->alamat_id }}" x-model="selectedAddress" class="mt-1 accent-[#63A2BB]">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-slate-800">{{ $address->label }} — {{ $address->nama_penerima }}</span>
                                        @if((int) ($address->is_utama ?? 0) === 1)
                                            <span class="rounded-full bg-[#63A2BB]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-[#63A2BB]">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $address->alamat_lengkap }}, {{ $address->kecamatan }}, {{ $address->kota }}, {{ $address->provinsi }} {{ $address->kode_pos }}</p>
                                    <p class="mt-2 text-xs text-slate-400">{{ $address->no_telepon }}</p>
                                </div>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-[#F8FAFB] p-8 text-center">
                                <p class="text-sm font-semibold text-slate-700">Belum ada alamat tersimpan.</p>
                                <a href="{{ route('profile.address.create.alias') }}?return=checkout" class="btn-primary mt-4 inline-flex px-5 py-3 text-sm">Tambah Alamat</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card-surface p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-sm font-black text-[#63A2BB]">C</div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Ekspedisi & Layanan</h2>
                            <p class="text-sm text-slate-500">Pilih layanan pengiriman yang sesuai.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($ekspedisis as $i => $eks)
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition-all duration-200" x-bind:class="selectedEkspedisi === {{ $eks->ekspedisi_id }} ? 'border-[#63A2BB] bg-[#63A2BB]/5 shadow-sm shadow-[#63A2BB]/10' : 'border-slate-200 bg-white hover:border-[#63A2BB]/40'">
                                <input type="radio" name="ekspedisi_id" value="{{ $eks->ekspedisi_id }}" x-model="selectedEkspedisi" @change="setOngkir({{ $eks->ongkir_flat ?? 0 }})" {{ $i === 0 ? 'checked' : '' }} class="accent-[#63A2BB]">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $eks->nama_ekspedisi }} — {{ $eks->jenis_layanan }}</p>
                                            <p class="mt-1 text-sm text-slate-500">Estimasi {{ $eks->estimasi_hari }} hari</p>
                                        </div>
                                        <div class="text-sm font-black text-[#63A2BB]">Rp {{ number_format((int) ($eks->ongkir_flat ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-[#F8FAFB] p-8 text-center text-sm text-slate-500">Ekspedisi belum tersedia.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card-surface p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-sm font-black text-[#63A2BB]">D</div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Metode Pembayaran</h2>
                            <p class="text-sm text-slate-500">Pilih metode pembayaran yang akan digunakan.</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        @foreach(['Transfer Bank', 'E-Wallet', 'QRIS', 'COD'] as $jenisLabel)
                            @php
                                $items = $metodesFlat->filter(function ($metode) use ($jenisLabel) {
                                    $jenis = strtolower((string) ($metode->jenis ?? ''));
                                    $nama = strtolower((string) ($metode->metode ?? ''));

                                    if ($jenisLabel === 'Transfer Bank') {
                                        return str_contains($jenis, 'transfer') || in_array($nama, ['bca', 'mandiri', 'bni']);
                                    }

                                    if ($jenisLabel === 'E-Wallet') {
                                        return str_contains($jenis, 'ewallet') || in_array($nama, ['gopay', 'ovo', 'dana']);
                                    }

                                    if ($jenisLabel === 'QRIS') {
                                        return str_contains($jenis, 'qris') || $nama === 'qris';
                                    }

                                    if ($jenisLabel === 'COD') {
                                        return str_contains($jenis, 'cod');
                                    }

                                    return false;
                                });
                            @endphp

                            <div class="rounded-3xl border border-slate-200 bg-[#F8FAFB] p-4">
                                <div class="mb-3 text-sm font-bold uppercase tracking-[0.18em] text-slate-500">{{ $jenisLabel }}</div>
                                <div class="space-y-2">
                                    @forelse($items as $metode)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition-all duration-200" x-bind:class="selectedMetode === {{ $metode->metode_id }} ? 'border-[#63A2BB] bg-[#63A2BB]/5 shadow-sm shadow-[#63A2BB]/10' : 'hover:border-[#63A2BB]/40'">
                                            <input type="radio" name="metode_id" value="{{ $metode->metode_id }}" x-model="selectedMetode" class="accent-[#63A2BB]">
                                            <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-2xl bg-[#63A2BB]/10 text-xs font-black text-[#63A2BB]">
                                                @if(!empty($metode->logo_url))
                                                    <img src="{{ $metode->logo_url }}" alt="{{ $metode->metode }}" class="h-full w-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($metode->metode ?? 'PM', 0, 2)) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-slate-800">{{ $metode->metode }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ $metode->instruksi ?? $metode->jenis ?? '' }}</p>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-4 text-xs text-slate-500">Tidak tersedia</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card-surface p-6" x-data="voucherWidget()">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#63A2BB]/10 text-sm font-black text-[#63A2BB]">E</div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Kode Voucher</h2>
                            <p class="text-sm text-slate-500">Masukkan kode untuk mendapatkan diskon.</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input type="text" x-model="kode" placeholder="Masukkan kode voucher" class="h-12 flex-1 rounded-full border border-slate-200 bg-[#F8FAFB] px-5 text-sm uppercase tracking-wide outline-none transition-all duration-200 focus:border-[#63A2BB] focus:ring-4 focus:ring-[#63A2BB]/20">
                        <button type="button" @click="apply()" class="btn-primary h-12 px-6 text-sm" :disabled="loading">Pakai Voucher</button>
                    </div>
                    <p x-show="message" x-cloak class="mt-3 text-sm font-medium" :class="valid ? 'text-emerald-600' : 'text-rose-500'" x-text="message"></p>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="card-surface p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-black text-slate-900">Ringkasan Pesanan</h2>

                    <div class="mt-5 max-h-72 space-y-3 overflow-y-auto pr-1">
                        @foreach($cartItems as $item)
                            @php
                                $produk = $item->detail->produk ?? null;
                                $gambar = $produk?->gambarUtama?->url_lengkap ?? $produk?->images?->first()?->url_lengkap ?? asset('images/placeholder.png');
                                $qty = (int) ($item->jumlah ?? 1);
                                $harga = (float) ($item->detail->harga ?? 0);
                                $line = $harga * $qty;
                            @endphp
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-[#F8FAFB] p-3">
                                <img src="{{ $gambar }}" alt="{{ $item->detail->nama_produk ?? 'Produk' }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-slate-800">{{ $item->detail->nama_produk ?? optional($produk)->nama_produk ?? '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Size: {{ $item->detail->ukuran ?? '-' }} · Qty: {{ $qty }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Warna: {{ optional($item->detail->warna)->nama_warna ?? '-' }}</p>
                                </div>
                                <div class="text-sm font-black text-[#63A2BB]">Rp {{ number_format((int) $line, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $stokWarnings = [];
                        foreach ($cartItems as $item) {
                            $stok = (int) ($item->detail->stok ?? 0);
                            $qty = (int) ($item->jumlah ?? 1);
                            $produkNama = $item->detail->nama_produk ?? optional($item->detail->produk)->nama_produk ?? 'Produk';
                            if ($stok < $qty) {
                                $stokWarnings[] = $produkNama . ' (stok: ' . $stok . ', diminta: ' . $qty . ')';
                            }
                        }
                    @endphp

                    @if(!empty($stokWarnings))
                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <div class="font-bold">Stok Produk Tidak Cukup</div>
                            <div class="mt-2 space-y-1 text-xs leading-relaxed">
                                @foreach($stokWarnings as $warning)
                                    <div>• {{ $warning }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-5 space-y-3 border-t border-slate-200 pt-5 text-sm">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold">Rp <span x-text="fmt(subtotalProduk)">{{ number_format((int) $subtotalServer, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold">Rp <span x-text="fmt(ongkir)">{{ number_format((int) $ongkirServer, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Biaya Layanan</span>
                            <span class="font-semibold">Rp <span x-text="fmt(biayaLayanan)">{{ number_format((int) $biayaLayananServer, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="flex items-center justify-between text-emerald-600" x-show="voucherDiscount > 0" x-cloak>
                            <span>Diskon Voucher</span>
                            <span class="font-semibold">-Rp <span x-text="fmt(voucherDiscount)">{{ number_format((int) $voucherDiscountServer, 0, ',', '.') }}</span></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                            <span class="text-base font-bold text-slate-900">Total</span>
                            <span class="text-xl font-black text-[#63A2BB]">Rp <span x-text="fmt(grandTotal)">{{ number_format((int) max(0, $subtotalServer + $ongkirServer + $biayaLayananServer - $voucherDiscountServer), 0, ',', '.') }}</span></span>
                        </div>
                    </div>

                    <form method="post" action="{{ route('checkout.process') }}" @submit.prevent="submitCheckout($event)" class="mt-6">
                        @csrf
                        <input type="hidden" name="alamat_id" x-ref="alamat">
                        <input type="hidden" name="ekspedisi_id" x-ref="ekspedisi">
                        <input type="hidden" name="metode_id" x-ref="metode">
                        <input type="hidden" name="voucher_id" x-ref="voucher" value="{{ $voucher->voucher_id ?? '' }}">
                        <button type="submit" {{ !empty($stokWarnings) ? 'disabled' : '' }} class="btn-primary w-full justify-center px-6 py-4 text-sm {{ !empty($stokWarnings) ? 'cursor-not-allowed opacity-50' : '' }}">Bayar Sekarang <span class="ml-1" x-text="'(' + fmt(grandTotal) + ')'">({{ number_format((int) max(0, $subtotalServer + $ongkirServer + $biayaLayananServer - $voucherDiscountServer), 0, ',', '.') }})</span></button>
                        <p class="mt-3 text-center text-xs text-slate-400">{{ empty($stokWarnings) ? 'Pastikan alamat, ekspedisi, dan metode pembayaran dipilih.' : 'Selesaikan masalah stok untuk melanjutkan.' }}</p>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function checkoutPage(state) {
    return {
        subtotalProduk: state.subtotalProduk,
        shippingMap: state.shippingMap || {},
        selectedEkspedisi: state.selectedEkspedisi || 0,
        selectedMetode: state.selectedMetode || 0,
        selectedAddress: state.selectedAddress || 0,
        biayaLayanan: 1000,
        voucherId: state.voucherId || 0,
        voucherDiscount: state.voucherDiscount || 0,
        get ongkir() {
            return Number(this.shippingMap[this.selectedEkspedisi] || 0);
        },
        get grandTotal() {
            return Math.max(0, (Number(this.subtotalProduk) + Number(this.ongkir) + Number(this.biayaLayanan)) - Number(this.voucherDiscount || 0));
        },
        init() {
            window.addEventListener('voucher-applied', (event) => {
                this.voucherDiscount = Number(event.detail?.diskon || 0);
                this.voucherId = Number(event.detail?.voucher_id || 0);
                if (this.$refs.voucher) {
                    this.$refs.voucher.value = this.voucherId || '';
                }
            });
        },
        fmt(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value || 0));
        },
        setOngkir(val) {
            this.selectedEkspedisi = Number(this.selectedEkspedisi || 0);
            this.shippingMap[this.selectedEkspedisi] = Number(val || 0);
        },
        submitCheckout(event) {
            if (!this.selectedAddress) {
                alert('Silakan pilih alamat pengiriman');
                return;
            }
            if (!this.selectedEkspedisi) {
                alert('Silakan pilih ekspedisi');
                return;
            }
            if (!this.selectedMetode) {
                alert('Silakan pilih metode pembayaran');
                return;
            }

            this.$refs.alamat.value = this.selectedAddress;
            this.$refs.ekspedisi.value = this.selectedEkspedisi;
            this.$refs.metode.value = this.selectedMetode;
            this.$refs.voucher.value = this.voucherId || '';
            event.target.submit();
        }
    };
}

function voucherWidget() {
    return {
        kode: '',
        loading: false,
        valid: false,
        message: '',
        async apply() {
            this.loading = true;
            this.valid = false;
            this.message = '';

            try {
                const response = await fetch('{{ route('checkout.apply_voucher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: new URLSearchParams({
                        kode_voucher: this.kode,
                        ekspedisi_id: document.querySelector('input[name="ekspedisi_id"]:checked')?.value || ''
                    }),
                });

                const data = await response.json();
                if (!response.ok || !data.valid) {
                    this.message = data.message || 'Voucher tidak valid';
                    return;
                }

                this.valid = true;
                this.message = data.diskon_text ? ('Voucher valid! ' + data.diskon_text) : 'Voucher valid!';
                window.dispatchEvent(new CustomEvent('voucher-applied', { detail: { diskon: data.diskon_amount, voucher_id: data.voucher_id } }));
            } catch (error) {
                this.message = 'Gagal memakai voucher';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection

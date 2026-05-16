@extends('layouts.buyer')

@section('title', 'MOVR | Checkout')

@section('content')
@php
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

<div class="space-y-6" x-data='checkoutPage(@json($checkoutState))'>
    <div>
        <div class="text-xs font-semibold text-cyan-300">CHECKOUT</div>
        <h1 class="text-2xl md:text-3xl font-black">Selesaikan Pembelian</h1>
    </div>

    @if(($cart ?? collect())->isEmpty())
        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
            <p class="text-slate-300">Item checkout belum dipilih.</p>
            <a href="{{ route('cart.index.alias') }}" class="mt-6 inline-flex rounded-full bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">
                Kembali ke Keranjang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8 space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <h2 class="font-bold text-lg">A. Informasi Penerima</h2>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="text-xs text-slate-400">Nama Lengkap</div>
                            <div class="font-semibold">{{ auth()->user()->nama_pengguna ?? '-' }}</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="text-xs text-slate-400">No. Telepon</div>
                            <div class="font-semibold">{{ auth()->user()->no_telepon ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-bold text-lg">B. Pilih Alamat Pengiriman</h2>
                        <a href="{{ route('profile.address.create.alias') }}?return=checkout" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">Tambah Alamat Baru</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($addresses as $address)
                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4">
                                <input type="radio" name="alamat_id" value="{{ $address->alamat_id }}" x-model="selectedAddress" class="mt-1 accent-cyan-400">
                                <div class="flex-1">
                                    <div class="font-semibold">{{ $address->label }} — {{ $address->nama_penerima }}</div>
                                    <div class="mt-1 text-sm text-slate-300">{{ $address->alamat_lengkap }}</div>
                                    <div class="mt-2 text-xs text-slate-400">{{ $address->kota }}, {{ $address->provinsi }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $address->no_telepon }}</div>
                                    @if((int) ($address->is_utama ?? 0) === 1)
                                        <div class="mt-2 inline-flex rounded-full border border-emerald-400/30 bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-300">Utama</div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 bg-black/20 p-6 text-center text-sm text-slate-300">
                                Belum ada alamat tersimpan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <h2 class="font-bold text-lg">C. Pilihan Ekspedisi & Layanan</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($ekspedisis as $exp)
                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4">
                                <input type="radio" name="ekspedisi_id" value="{{ $exp->ekspedisi_id }}" x-model="selectedEkspedisi" class="mt-1 accent-cyan-400" {{ $loop->first ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 font-semibold">
                                                @if(!empty($exp->logo_url))
                                                    <img src="{{ $exp->logo_url }}" alt="{{ $exp->nama_ekspedisi }}" class="h-6 w-6 rounded object-cover">
                                                @endif
                                                {{ $exp->nama_ekspedisi }} — {{ $exp->jenis_layanan }}
                                            </div>
                                            <div class="mt-1 text-sm text-slate-300">Estimasi {{ $exp->estimasi_hari }} hari</div>
                                        </div>
                                        <div class="text-sm font-bold text-white">{{ 'Rp ' . number_format((int) ($exp->ongkir_flat ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 bg-black/20 p-6 text-center text-sm text-slate-300">
                                Ekspedisi belum tersedia.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <h2 class="font-bold text-lg">D. Metode Pembayaran</h2>
                    <div class="mt-4 space-y-5 text-sm">
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

                            <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                                <div class="font-bold">{{ $jenisLabel }}</div>
                                <div class="mt-3 space-y-2">
                                    @forelse($items as $metode)
                                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4">
                                            <input type="radio" name="metode_id" value="{{ $metode->metode_id }}" x-model="selectedMetode" class="mt-1 accent-cyan-400" {{ $loop->first && $jenisLabel === 'Transfer Bank' ? 'checked' : '' }}>
                                            <div class="flex items-start gap-3">
                                                @if(!empty($metode->logo_url))
                                                    <img src="{{ $metode->logo_url }}" alt="{{ $metode->metode }}" class="h-10 w-10 rounded-xl object-cover">
                                                @else
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-xs font-bold text-white">{{ strtoupper(substr($metode->metode ?? 'PM', 0, 2)) }}</div>
                                                @endif
                                                <div>
                                                    <div class="font-semibold">{{ $metode->metode }}</div>
                                                    <div class="mt-1 text-xs text-slate-400">{{ $metode->instruksi ?? $metode->jenis ?? '' }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-xs text-slate-400">Tidak tersedia</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-xs text-slate-400">Pilih metode pembayaran untuk melanjutkan.</div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5" x-data="voucherWidget()">
                    <h2 class="font-bold text-lg">E. Kode Voucher</h2>
                    <form method="post" action="{{ route('checkout.apply_voucher') }}" @submit.prevent="apply()" class="mt-4 flex gap-3">
                        @csrf
                        <input type="text" x-model="kode" class="flex-1 rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400" placeholder="Masukkan kode voucher">
                        <button type="submit" class="rounded-2xl bg-cyan-500 px-5 py-2 text-sm font-bold text-slate-950 hover:bg-cyan-400" :disabled="loading">Pakai</button>
                    </form>
                    <div class="mt-3 text-sm">
                        <div class="text-emerald-300" x-show="valid" x-text="message"></div>
                        <div class="text-rose-300" x-show="!valid && message" x-text="message"></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 lg:sticky lg:top-24">
                    <h2 class="font-bold text-lg">Ringkasan Pesanan</h2>

                    <div class="mt-4 space-y-3">
                        @foreach($cart as $item)
                            @php
                                $produk = $item->detail->produk ?? null;
                                $image = optional(optional($produk)->images->first())->url_lengkap ?? (optional(optional($produk)->images->first())->url_gambar ? asset('storage/' . $produk->images->first()->url_gambar) : asset('images/default-product.svg'));
                                $qty = (int) ($item->jumlah ?? 1);
                                $harga = (float) ($item->detail->harga ?? 0);
                                $line = $harga * $qty;
                            @endphp
                            <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-3">
                                <img src="{{ $image }}" alt="{{ $item->detail->nama_produk ?? 'Produk' }}" class="h-16 w-16 rounded-xl object-cover">
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ $item->detail->nama_produk ?? optional($produk)->nama_produk ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-400">Size: {{ $item->detail->ukuran ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">Warna: {{ optional($item->detail->warna)->nama_warna ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-400">Qty: {{ $qty }}</div>
                                </div>
                                <div class="text-sm font-bold">Rp {{ number_format((int) $line, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $stokWarnings = [];
                        foreach ($cart as $item) {
                            $stok = (int) ($item->detail->stok ?? 0);
                            $qty = (int) ($item->jumlah ?? 1);
                            $produkNama = $item->detail->nama_produk ?? optional($item->detail->produk)->nama_produk ?? 'Produk';
                            if ($stok < $qty) {
                                $stokWarnings[] = $produkNama . ' (stok: ' . $stok . ', diminta: ' . $qty . ')';
                            }
                        }
                    @endphp

                    @if(!empty($stokWarnings))
                    <div class="mt-4 rounded-2xl border border-rose-300/50 bg-rose-50/20 p-4 backdrop-blur-sm">
                        <div class="flex items-start gap-3">
                            <div class="text-rose-500 text-lg mt-0.5">⚠️</div>
                            <div>
                                <div class="font-semibold text-rose-400">Stok Produk Tidak Cukup</div>
                                <div class="mt-2 space-y-1 text-xs text-rose-300/80">
                                    @foreach($stokWarnings as $warning)
                                        <div>• {{ $warning }}</div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-xs text-rose-300">
                                    Silakan kembali ke keranjang dan sesuaikan jumlah pembelian.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-5 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-300">Subtotal Produk</span><span class="font-bold">Rp <span x-text="fmt(subtotalProduk)">{{ number_format((int) $subtotalServer, 0, ',', '.') }}</span></span></div>
                        <div class="flex justify-between"><span class="text-slate-300">Ongkos Kirim</span><span class="font-bold">Rp <span x-text="fmt(ongkir)">{{ number_format((int) $ongkirServer, 0, ',', '.') }}</span></span></div>
                        <div class="flex justify-between"><span class="text-slate-300">Biaya Layanan</span><span class="font-bold">Rp <span x-text="fmt(biayaLayanan)">{{ number_format((int) $biayaLayananServer, 0, ',', '.') }}</span></span></div>
                        <div class="flex justify-between" x-show="voucherDiscount > 0"><span class="text-slate-300">Diskon Voucher</span><span class="font-bold">-Rp <span x-text="fmt(voucherDiscount)">{{ number_format((int) $voucherDiscountServer, 0, ',', '.') }}</span></span></div>
                        <div class="border-t border-white/10 pt-3 flex justify-between">
                            <span class="text-slate-200 font-semibold">Total</span>
                            <span class="font-black text-xl">Rp <span x-text="fmt(grandTotal)">{{ number_format((int) max(0, $subtotalServer + $ongkirServer + $biayaLayananServer - $voucherDiscountServer), 0, ',', '.') }}</span></span>
                        </div>
                    </div>

                    <form method="post" action="{{ route('checkout.process') }}" @submit.prevent="submitCheckout($event)" class="mt-5">
                        @csrf
                        <input type="hidden" name="alamat_id" x-ref="alamat">
                        <input type="hidden" name="ekspedisi_id" x-ref="ekspedisi">
                        <input type="hidden" name="metode_id" x-ref="metode">
                        <input type="hidden" name="voucher_id" x-ref="voucher" value="{{ $voucher->voucher_id ?? '' }}">
                        <button type="submit" {{ !empty($stokWarnings) ? 'disabled' : '' }} class="w-full rounded-3xl px-6 py-3 text-sm font-bold text-slate-950 transition-all {{ !empty($stokWarnings) ? 'bg-slate-400 cursor-not-allowed opacity-50' : 'bg-cyan-500 hover:bg-cyan-400' }}">Bayar Sekarang</button>
                        <div class="mt-2 text-xs text-slate-400">{{ empty($stokWarnings) ? 'Pastikan alamat, ekspedisi, dan metode pembayaran dipilih.' : 'Selesaikan masalah stok untuk melanjutkan.' }}</div>
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

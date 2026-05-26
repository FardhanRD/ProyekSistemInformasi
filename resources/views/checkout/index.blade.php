{{--
  FILE: resources/views/checkout/index.blade.php
  Halaman checkout 2 kolom:
  - Kolom Kiri: Informasi Penerima, Alamat Pengiriman, Ekspedisi & Layanan, Metode Pembayaran, Voucher
  - Kolom Kanan: Ringkasan Pesanan (produk + perhitungan ongkir/layanan/diskon/total)
--}}

@extends('layouts.buyer')

@section('title', __('ui.checkout') . ' | MOVR')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold text-cyan-300">{{ __('ui.checkout') }}</div>
        <h1 class="text-2xl md:text-3xl font-black">{{ __('ui.complete_purchase') }}</h1>
    </div>

    @php
        // Kalkulasi subtotal produk dari server-side untuk inisialisasi Alpine.js
        $subtotalProduk = $cart->reduce(function ($carry, $item) {
            return $carry + ((float)($item->detail->harga ?? 0) * (int)($item->jumlah ?? 1));
        }, 0);
        $ekspedisiPertama = $ekspedisis->first();
        $shippingGroups = $ekspedisis->groupBy('nama_ekspedisi')->map(function ($items, $namaEkspedisi) {
            return [
                'nama_ekspedisi' => $namaEkspedisi,
                'items' => $items->values()->map(function ($e) {
                    return [
                        'ekspedisi_id' => (int) ($e->ekspedisi_id ?? 0),
                        'jenis_layanan' => (string) ($e->jenis_layanan ?? ''),
                        'estimasi_hari' => (string) ($e->estimasi_hari ?? ''),
                        'ongkir_flat' => (int) ($e->ongkir_flat ?? 0),
                        'logo_url' => (string) ($e->logo_url ?? ''),
                    ];
                })->all(),
            ];
        })->values();
        $selectedShippingFirst = $shippingGroups->first()['items'][0] ?? null;

        // alias lebih ringkas untuk view yang mengharapkan 'subtotal'
        $subtotal = $subtotalProduk;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="checkoutData({{ $subtotal ?? 0 }}, {{ $ekspedisiPertama->ekspedisi_id ?? 0 }}, {{ (int)($ekspedisiPertama->ongkir_flat ?? 0) }})">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- A. Informasi Penerima --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">A. {{ __('ui.recipient_info') }}</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-xs text-slate-400">{{ __('ui.full_name') }}</div>
                        <div class="font-semibold">{{ auth()->user()->nama_pengguna ?? '-' }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-xs text-slate-400">{{ __('ui.phone_number') }}</div>
                        <div class="font-semibold">{{ auth()->user()->no_telepon ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- B. Pilih Alamat Pengiriman --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">B. {{ __('ui.shipping_address') }}</h2>

                @php
                    $alamatUtama = $addresses->firstWhere('is_utama', 1) ?? $addresses->first();
                @endphp

                <div class="mt-4 space-y-3">
                    @foreach($addresses as $a)
                        <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4 cursor-pointer">
                            <input type="radio"
                                   name="alamat_id"
                                   value="{{ $a->alamat_id }}"
                                   class="mt-1 accent-cyan-400"
                                   {{ ($a->alamat_id ?? null) === ($alamatUtama->alamat_id ?? null) ? 'checked' : '' }} />
                            <div class="flex-1">
                                <div class="font-semibold">{{ $a->label }} — {{ $a->nama_penerima }}</div>
                                <div class="text-sm text-slate-300 mt-1">{{ $a->alamat_lengkap }}</div>
                                <div class="text-xs text-slate-400 mt-2">{{ $a->kota }}, {{ $a->provinsi }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ $a->no_telepon }}</div>

                                @if(($a->is_utama ?? false) == 1)
                                    <div class="mt-2 inline-flex rounded-full bg-emerald-500/15 border border-emerald-400/30 px-3 py-1 text-xs text-emerald-300 font-semibold">{{ __('ui.shipping_primary') }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 text-sm text-slate-300 flex items-center gap-3">
                    <a href="{{ route('profile.address.create', ['return' => 'checkout']) }}"
                       class="inline-flex rounded-full bg-white/5 border border-white/10 px-5 py-3 text-sm font-bold hover:bg-white/10">
                        {{ __('ui.address_add_new') }}
                    </a>
                </div>
            </div>

            {{-- C. Pilihan Ekspedisi & Layanan --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">C. {{ __('ui.shipping_service') }}</h2>

                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <button type="button"
                                class="flex w-full items-center justify-between gap-3 text-left"
                                @click="shippingOpen = !shippingOpen; serviceOpen = false">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Ekspedisi</div>
                                <div class="mt-1 font-semibold text-white" x-text="selectedShippingGroupLabel || '{{ __('ui.select_shipping') }}'"></div>
                            </div>
                            <svg class="h-5 w-5 text-slate-300 transition-transform" :class="shippingOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="shippingOpen" x-cloak class="mt-4 space-y-2 border-t border-white/10 pt-4">
                            <template x-for="(group, groupIndex) in shippingGroups" :key="group.nama_ekspedisi">
                                <button type="button"
                                        class="flex w-full items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-left transition hover:border-cyan-400/50 hover:bg-cyan-400/10"
                                        :class="selectedShippingGroupIndex === groupIndex ? 'border-cyan-400 bg-cyan-400/10' : ''"
                                        @click="selectShippingGroup(groupIndex)">
                                    <div>
                                        <div class="font-semibold text-white" x-text="group.nama_ekspedisi"></div>
                                        <div class="mt-1 text-xs text-slate-400" x-text="group.items.length + ' {{ __('ui.services_available') }}'"></div>
                                    </div>
                                    <span class="text-xs font-bold text-cyan-300">{{ __('ui.select') }}</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <button type="button"
                                class="flex w-full items-center justify-between gap-3 text-left"
                                @click="serviceOpen = !serviceOpen"
                                :disabled="!selectedShippingGroup">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Layanan</div>
                                <div class="mt-1 font-semibold text-white" x-text="selectedServiceLabel || '{{ __('ui.select_service') }}'"></div>
                            </div>
                            <svg class="h-5 w-5 text-slate-300 transition-transform" :class="serviceOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="serviceOpen && selectedShippingGroup" x-cloak class="mt-4 space-y-2 border-t border-white/10 pt-4">
                            <template x-for="service in selectedShippingServices" :key="service.ekspedisi_id">
                                <button type="button"
                                        class="flex w-full items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-left transition hover:border-cyan-400/50 hover:bg-cyan-400/10"
                                        :class="selectedShippingId === service.ekspedisi_id ? 'border-cyan-400 bg-cyan-400/10' : ''"
                                        @click="selectShippingService(service)">
                                    <div class="flex items-start gap-3">
                                        <template x-if="service.logo_url">
                                            <img :src="service.logo_url" class="h-10 w-10 rounded-xl object-cover" alt="logo">
                                        </template>
                                        <div>
                                            <div class="font-semibold text-white">
                                                <span x-text="service.jenis_layanan"></span>
                                            </div>
                                            <div class="mt-1 text-xs text-slate-400">Estimasi <span x-text="service.estimasi_hari"></span> hari</div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-white">Rp <span x-text="fmt(service.ongkir_flat)"></span></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- D. Metode Pembayaran --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">D. {{ __('ui.payment_method_title') }}</h2>

                @php
                    $metodes = $metodes ?? collect();
                    // jika controller mengirim grouped collection, flatten untuk kompatibilitas view
                    if($metodes->isNotEmpty() && $metodes->first() instanceof \Illuminate\Support\Collection){
                        $metodesFlat = $metodes->flatten(1);
                    } else {
                        $metodesFlat = $metodes;
                    }
                    // fallback bila kolom jenis tidak sesuai nilai seed
                    $allJenis = $metodesFlat->pluck('jenis')->unique()->values()->all();
                @endphp

                <div class="mt-4 space-y-5 text-sm">
                    @foreach(['Transfer Bank','E-Wallet','QRIS','COD'] as $jenisLabel)
                        @php
                            $items = $metodesFlat->filter(function($m) use ($jenisLabel) {
                                $j = strtolower((string)($m->jenis ?? ''));
                                $metode = strtolower((string)($m->metode ?? ''));
                                if ($jenisLabel === 'Transfer Bank') return str_contains($j,'transfer') || in_array($metode,['bca','mandiri','bni']);
                                if ($jenisLabel === 'E-Wallet') return str_contains($j,'ewallet') || in_array($metode,['gopay','ovo','dana']);
                                if ($jenisLabel === 'QRIS') return str_contains($j,'qris') || $metode==='qris';
                                if ($jenisLabel === 'COD') return str_contains($j,'cod');
                                return false;
                            });
                        @endphp

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="font-bold">{{ $jenisLabel }}</div>
                            <div class="mt-3 space-y-2">
                                @foreach($items as $k => $m)
                                    <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4 cursor-pointer">
                                             <input type="radio"
                                                 name="metode_id"
                                                 value="{{ $m->metode_id }}"
                                                 class="mt-1 accent-cyan-400"
                                                 {{ $metodesFlat->isNotEmpty() && $k === 0 && $jenisLabel === 'Transfer Bank' ? 'checked' : '' }} />
                                        <div class="flex items-start gap-3">
                                            @if(!empty($m->logo_url))
                                                <img src="{{ $m->logo_url }}" class="w-10 h-10 rounded-xl object-cover" alt="{{ $m->metode }}" />
                                            @endif
                                            <div>
                                                <div class="font-semibold">{{ $m->metode }}</div>
                                                <div class="text-xs text-slate-400 mt-1">{{ $m->jenis ?? '' }}</div>
                                                {{-- nomor rekening untuk transfer bank bisa hardcode jika dibutuhkan --}}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach

                                @if($items->isEmpty())
                                    <div class="text-xs text-slate-400">Tidak tersedia</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-xs text-slate-400">{{ __('ui.choose_payment_method') }}</div>
            </div>

            {{-- E. Kode Voucher --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5" x-data="voucherUI()">
                <h2 class="font-bold text-lg">E. {{ __('ui.voucher_title') }}</h2>

                <form method="post" action="{{ route('checkout.apply_voucher') }}" @submit.prevent="apply()" class="mt-4 flex gap-3">
                    @csrf
                    <input type="text"
                           x-model="kode"
                           class="flex-1 rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400"
                           placeholder="{{ __('ui.voucher_placeholder') }}" />
                    <button type="submit"
                            class="rounded-2xl bg-cyan-500 px-5 py-2 text-sm font-bold text-slate-950 hover:bg-cyan-400 disabled:opacity-50"
                            :disabled="loading">
                        {{ __('ui.use') }}
                    </button>
                </form>

                <div class="mt-3 text-sm">
                    <div class="text-emerald-300" x-show="valid" x-text="message"></div>
                    <div class="text-rose-300" x-show="!valid && message" x-text="message"></div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Ringkasan Pesanan --}}
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-24 rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">{{ __('ui.summary') }}</h2>

                <div class="mt-4 space-y-3">
                    @php
                        $subtotal = 0;
                    @endphp
                    @foreach($cart as $c)
                        @php
                            $qty = (int)($c->jumlah ?? 1);
                            $harga = (float)($c->detail->harga ?? 0);
                            $line = $harga * $qty;
                            $subtotal += $line;
                        @endphp
                        <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-3">
                            <img src="{{ $c->detail->produk->gambar_url ?? ($c->detail->produk->gambar ?? '') }}" class="w-16 h-16 rounded-xl object-cover" alt="produk" />
                            <div class="flex-1">
                                <div class="font-semibold text-sm">{{ $c->detail->nama_produk ?? $c->detail->produk->nama_produk ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-1">Size: {{ $c->detail->ukuran ?? '-' }}</div>
                                <div class="text-xs text-slate-400">Warna: {{ $c->detail->warna ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-1">Qty: {{ $qty }}</div>
                            </div>
                            <div class="text-sm font-bold">Rp {{ number_format((int)$line,0,',','.') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-300">{{ __('ui.cart_products_subtotal') }}</span><span class="font-bold">Rp <span x-text="fmt(subtotalProduk)"></span></span></div>
                    <div class="flex justify-between"><span class="text-slate-300">{{ __('ui.cart_shipping_cost') }}</span><span class="font-bold">Rp <span x-text="fmt(ongkir)"></span></span></div>
                    <div class="flex justify-between"><span class="text-slate-300">{{ __('ui.cart_service_fee') }}</span><span class="font-bold">Rp <span x-text="fmt(biayaLayanan)"></span></span></div>
                    <div class="flex justify-between" x-show="diskonVoucher>0"><span class="text-slate-300">{{ __('ui.voucher_discount') }}</span><span class="font-bold">-Rp <span x-text="fmt(diskonVoucher)"></span></span></div>
                    <div class="border-t border-white/10 pt-3 flex justify-between">
                        <span class="text-slate-200 font-semibold">{{ __('ui.total') }}</span>
                        <span class="font-black text-xl">Rp <span x-text="fmt(grandTotal)"></span></span>
                    </div>
                </div>

                {{-- Tombol Bayar Sekarang --}}
                <div class="mt-5">
                    {{-- onsubmit diganti dengan Alpine untuk validasi yang lebih bersih --}}
                    <form method="post" action="{{ route('checkout.process') }}" @submit.prevent="submitCheckout($event)">
                        @csrf

                        {{-- kirim nilai hidden untuk backend --}}
                        <input type="hidden" name="alamat_id" x-ref="alamat" />
                        <input type="hidden" name="ekspedisi_id" x-ref="ekspedisi" />
                        <input type="hidden" name="metode_id" x-ref="metode" />
                        <input type="hidden" name="voucher_id" x-ref="voucher" value="{{ $voucher->voucher_id ?? '' }}" />

                        <button type="submit" class="w-full rounded-3xl bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">
                            {{ __('ui.pay_now') }}
                        </button>

                        <div class="mt-2 text-xs text-slate-400">{{ __('ui.ensure_selected_checkout') }}</div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // simple format uang
    function formatRupiah(n){
        const v = Number(n||0);
        return new Intl.NumberFormat('id-ID').format(v);
    }

    function checkoutData(subtotalProduk, initialEkspedisiId, initialOngkir) {
        return {
            // Data dari PHP
            subtotalProduk: subtotalProduk,
            ekspedisiList: {!! $ekspedisis->mapWithKeys(fn($e) => [$e->ekspedisi_id => (int)$e->ongkir_flat])->toJson() !!},
            shippingGroups: {!! $shippingGroups->toJson() !!},

            // State reaktif
            shippingOpen: false,
            serviceOpen: false,
            selectedShippingGroupIndex: 0,
            selectedShippingId: initialEkspedisiId,
            diskonVoucher: 0,
            voucherId: null,
            biayaLayanan: 1000,

            // Computed properties
            get selectedShippingGroup() {
                return this.shippingGroups[this.selectedShippingGroupIndex] || null;
            },
            get selectedShippingServices() {
                return this.selectedShippingGroup ? (this.selectedShippingGroup.items || []) : [];
            },
            get selectedShippingGroupLabel() {
                return this.selectedShippingGroup ? this.selectedShippingGroup.nama_ekspedisi : '';
            },
            get selectedServiceLabel() {
                const service = this.selectedShippingServices.find((item) => Number(item.ekspedisi_id) === Number(this.selectedShippingId));
                return service ? service.jenis_layanan : '';
            },
            get ongkir() {
                return this.ekspedisiList[this.selectedShippingId] || 0;
            },
            get grandTotal() {
                return Math.max(0, (this.subtotalProduk + this.ongkir + this.biayaLayanan) - this.diskonVoucher);
            },

            // Inisialisasi
            init() {
                // Dengar event dari komponen voucher
                this.$watch('selectedShippingId', (val) => {
                    // Jika ada voucher ongkir, perlu re-apply
                    console.log('Ekspedisi berubah, ongkir baru: ', this.ongkir);
                });

                const initialGroupIndex = this.shippingGroups.findIndex((group) => {
                    return (group.items || []).some((item) => Number(item.ekspedisi_id) === Number(initialEkspedisiId));
                });
                this.selectedShippingGroupIndex = initialGroupIndex >= 0 ? initialGroupIndex : 0;
                const currentGroup = this.shippingGroups[this.selectedShippingGroupIndex];
                const firstItem = currentGroup && currentGroup.items && currentGroup.items.length ? currentGroup.items[0] : null;
                if (!this.selectedShippingId && firstItem) {
                    this.selectedShippingId = firstItem.ekspedisi_id;
                }

                // baca nilai voucher awal dari hidden input bila ada
                this.voucherId = document.querySelector('input[name="voucher_id"]')?.value || null;
                window.addEventListener('voucher-applied', (e) => {
                    this.diskonVoucher = e.detail.diskon || 0;
                    this.voucherId = e.detail.voucher_id ?? this.voucherId;
                    if(this.$refs && this.$refs.voucher) this.$refs.voucher.value = this.voucherId || '';
                });
            },

            // Helper
            fmt(n) { return formatRupiah(n); },

            // Aksi
            selectShippingGroup(index) {
                this.selectedShippingGroupIndex = Number(index);
                const group = this.shippingGroups[this.selectedShippingGroupIndex];
                const firstItem = group && group.items && group.items.length ? group.items[0] : null;
                if (firstItem) {
                    this.selectedShippingId = Number(firstItem.ekspedisi_id);
                }
                this.shippingOpen = false;
                this.serviceOpen = true;
            },

            selectShippingService(service) {
                this.selectedShippingId = Number(service.ekspedisi_id);
                this.shippingOpen = false;
                this.serviceOpen = false;
            },

            submitCheckout(event) {
                const alamat = document.querySelector('input[name="alamat_id"]:checked');
                const metode = document.querySelector('input[name="metode_id"]:checked');

                if (!alamat) { alert('{{ __('ui.select_address_alert') }}'); return; }
                if (!this.selectedShippingId) { alert('{{ __('ui.select_shipping_alert') }}'); return; }
                if (!metode) { alert('{{ __('ui.select_payment_alert') }}'); return; }

                // Sinkronkan nilai ke hidden input sebelum submit
                this.$refs.alamat.value = alamat.value;
                this.$refs.ekspedisi.value = this.selectedShippingId;
                this.$refs.metode.value = metode.value;
                if(this.$refs && this.$refs.voucher) this.$refs.voucher.value = this.voucherId || '';

                event.target.submit();
            }
        }
    }

    /*
    // Fungsi lama, digantikan oleh checkoutData
    function checkoutState() {
        return {
            ongkir: 0,
            diskonVoucher: 0,
            shippingSelected: null,
            syncShipping(){
                const checked = document.querySelector('input[name="ekspedisi_id"]:checked');
                if(!checked) return;
                this.shippingSelected = checked.value;
                // ambil ongkir dari label teks terdekat: pendekatan aman adalah data tidak tersedia di view
                // jadi ringkasan akan di-refresh saat apply voucher / submit.
                // placeholder 0 untuk ongkir di sini.
            }
        } 
    }
    */

    function voucherUI(){
        return {
            kode: '',
            loading: false,
            valid: false,
            message: '',
            async apply(){
                window.dispatchEvent(new CustomEvent('voucher-applied', { detail: { diskon: 0 } })); // Reset diskon
                if(!this.kode.trim()) return;
                this.loading = true;
                this.valid = false;
                this.message = '';
                try{
                    const resp = await fetch('{{ route('checkout.apply_voucher') }}',{
                        method:'POST',
                        headers:{
                            'Content-Type':'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':'application/json'
                        },
                        body: new URLSearchParams({kode_voucher: this.kode, ekspedisi_id: this.selectedShippingId || ''})
                    });
                    const data = await resp.json();
                    if(!resp.ok || !data.valid){
                        this.valid = false;
                        this.message = data.message || '{{ __('ui.voucher_invalid') }}';
                        return;
                    }
                    this.valid = true;
                    this.message = data.diskon_text ? ('Voucher valid! ' + data.diskon_text) : 'Voucher valid!';
                    // Kirim event dengan nilai diskon agar UI ringkasan bisa update
                    window.dispatchEvent(new CustomEvent('voucher-applied', { detail: { diskon: data.diskon_amount, voucher_id: data.voucher_id ?? null } }));
                    // update hidden input if present
                    const hv = document.querySelector('input[name="voucher_id"]');
                    if(hv && data.voucher_id) hv.value = data.voucher_id;
                }catch(e){
                    this.message = '{{ __('ui.voucher_failed') }}';
                }finally{
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection

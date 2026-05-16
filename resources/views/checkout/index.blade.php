{{--
  FILE: resources/views/checkout/index.blade.php
  Halaman checkout 2 kolom:
  - Kolom Kiri: Informasi Penerima, Alamat Pengiriman, Ekspedisi & Layanan, Metode Pembayaran, Voucher
  - Kolom Kanan: Ringkasan Pesanan (produk + perhitungan ongkir/layanan/diskon/total)
--}}

@extends('layouts.buyer')

@section('title','MOVR | Checkout')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold text-cyan-300">CHECKOUT</div>
        <h1 class="text-2xl md:text-3xl font-black">Selesaikan Pembelian</h1>
    </div>

    @php
        // Kalkulasi subtotal produk dari server-side untuk inisialisasi Alpine.js
        $subtotalProduk = $cart->reduce(function ($carry, $item) {
            return $carry + ((float)($item->detail->harga ?? 0) * (int)($item->jumlah ?? 1));
        }, 0);
        $ekspedisiPertama = $ekspedisis->first();

        // alias lebih ringkas untuk view yang mengharapkan 'subtotal'
        $subtotal = $subtotalProduk;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="checkoutData({{ $subtotal ?? 0 }}, {{ $ekspedisiPertama->ekspedisi_id ?? 0 }}, {{ (int)($ekspedisiPertama->ongkir_flat ?? 0) }})">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- A. Informasi Penerima --}}
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

            {{-- B. Pilih Alamat Pengiriman --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">B. Pilih Alamat Pengiriman</h2>

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
                                    <div class="mt-2 inline-flex rounded-full bg-emerald-500/15 border border-emerald-400/30 px-3 py-1 text-xs text-emerald-300 font-semibold">Utama</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 text-sm text-slate-300 flex items-center gap-3">
                    <a href="{{ route('profile.address.create', ['return' => 'checkout']) }}"
                       class="inline-flex rounded-full bg-white/5 border border-white/10 px-5 py-3 text-sm font-bold hover:bg-white/10">
                        Tambah Alamat Baru
                    </a>
                </div>
            </div>

            {{-- C. Pilihan Ekspedisi & Layanan --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">C. Pilihan Ekspedisi & Layanan</h2>

                @php
                    // Pastikan ekpedisi termurah berada di urutan pertama
                    $ekspedisisSorted = $ekspedisis; // Sudah diurutkan di controller
                @endphp

                <div class="mt-4 space-y-3">
                    @foreach($ekspedisisSorted as $i => $e)
                        <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/20 p-4 cursor-pointer">
                            <input type="radio"
                                   name="ekspedisi_id"
                                   value="{{ $e->ekspedisi_id }}"
                                   x-model="selectedEkspedisi"
                                   class="mt-1 accent-cyan-400"
                                   {{ $i === 0 ? 'checked' : '' }} />
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold flex items-center gap-2">
                                            @if(!empty($e->logo_url))
                                                <img src="{{ $e->logo_url }}" class="w-6 h-6 rounded object-cover" alt="logo" />
                                            @endif
                                            {{ $e->nama_ekspedisi }} — {{ $e->jenis_layanan }}
                                        </div>
                                        <div class="text-sm text-slate-300 mt-1">Estimasi {{ $e->estimasi_hari }} hari</div>
                                    </div>
                                    <div class="text-sm font-bold text-white">
                                        Rp {{ number_format((int)($e->ongkir_flat ?? 0),0,',','.') }}
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- D. Metode Pembayaran --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <h2 class="font-bold text-lg">D. Metode Pembayaran</h2>

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

                <div class="mt-4 text-xs text-slate-400">Pilih metode pembayaran untuk melanjutkan.</div>
            </div>

            {{-- E. Kode Voucher --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5" x-data="voucherUI()">
                <h2 class="font-bold text-lg">E. Kode Voucher</h2>

                <form method="post" action="{{ route('checkout.apply_voucher') }}" @submit.prevent="apply()" class="mt-4 flex gap-3">
                    @csrf
                    <input type="text"
                           x-model="kode"
                           class="flex-1 rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400"
                           placeholder="Masukkan kode voucher" />
                    <button type="submit"
                            class="rounded-2xl bg-cyan-500 px-5 py-2 text-sm font-bold text-slate-950 hover:bg-cyan-400 disabled:opacity-50"
                            :disabled="loading">
                        {{ __('Pakai') }}
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
                <h2 class="font-bold text-lg">Ringkasan Pesanan</h2>

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
                    <div class="flex justify-between"><span class="text-slate-300">Subtotal Produk</span><span class="font-bold">Rp <span x-text="fmt(subtotalProduk)"></span></span></div>
                    <div class="flex justify-between"><span class="text-slate-300">Ongkos Kirim</span><span class="font-bold">Rp <span x-text="fmt(ongkir)"></span></span></div>
                    <div class="flex justify-between"><span class="text-slate-300">Biaya Layanan</span><span class="font-bold">Rp <span x-text="fmt(biayaLayanan)"></span></span></div>
                    <div class="flex justify-between" x-show="diskonVoucher>0"><span class="text-slate-300">Diskon Voucher</span><span class="font-bold">-Rp <span x-text="fmt(diskonVoucher)"></span></span></div>
                    <div class="border-t border-white/10 pt-3 flex justify-between">
                        <span class="text-slate-200 font-semibold">Total</span>
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
                            Bayar Sekarang
                        </button>

                        <div class="mt-2 text-xs text-slate-400">Pastikan alamat, ekspedisi, dan metode pembayaran dipilih.</div>
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

            // State reaktif
            selectedEkspedisi: initialEkspedisiId,
            diskonVoucher: 0,
            voucherId: null,
            biayaLayanan: 1000,

            // Computed properties
            get ongkir() {
                return this.ekspedisiList[this.selectedEkspedisi] || 0;
            },
            get grandTotal() {
                return Math.max(0, (this.subtotalProduk + this.ongkir + this.biayaLayanan) - this.diskonVoucher);
            },

            // Inisialisasi
            init() {
                // Dengar event dari komponen voucher
                this.$watch('selectedEkspedisi', (val) => {
                    // Jika ada voucher ongkir, perlu re-apply
                    console.log('Ekspedisi berubah, ongkir baru: ', this.ongkir);
                });

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
            submitCheckout(event) {
                const alamat = document.querySelector('input[name="alamat_id"]:checked');
                const ekspedisi = document.querySelector('input[name="ekspedisi_id"]:checked');
                const metode = document.querySelector('input[name="metode_id"]:checked');

                if (!alamat) { alert('Silakan pilih alamat pengiriman'); return; }
                if (!ekspedisi) { alert('Silakan pilih ekspedisi'); return; }
                if (!metode) { alert('Silakan pilih metode pembayaran'); return; }

                // Sinkronkan nilai ke hidden input sebelum submit
                this.$refs.alamat.value = alamat.value;
                this.$refs.ekspedisi.value = ekspedisi.value;
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
                        body: new URLSearchParams({kode_voucher: this.kode, ekspedisi_id: document.querySelector('input[name="ekspedisi_id"]:checked')?.value || ''})
                    });
                    const data = await resp.json();
                    if(!resp.ok || !data.valid){
                        this.valid = false;
                        this.message = data.message || 'Voucher tidak valid';
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
                    this.message = 'Gagal memakai voucher';
                }finally{
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection

{{--
  // ── FILE: resources/views/product/detail.blade.php ──
  Product detail: image slider + variant picker + qty + add cart/wishlist + reviews.
--}}

@extends('layouts.buyer')

@section('title', 'MOVR | Produk')

@section('content')
@php
    $produk = $produk ?? null;
@endphp

<div class="space-y-6">

    @if(!$produk)
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-slate-300">Produk tidak ditemukan.</div>
    @else

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Images --}}
            <section class="lg:col-span-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                    <div x-data="imageGallery({{ $imagesJson ?? '[]' }})" class="space-y-3">
                        <div class="relative">
                            <img :src="current().url" :alt="current().alt" class="w-full h-[360px] object-cover rounded-2xl border border-white/10" />
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(img, idx) in images" :key="idx">
                                <button type="button"
                                        class="rounded-2xl border border-white/10 overflow-hidden hover:ring-2 hover:ring-cyan-400"
                                        :class="idx===active ? 'ring-2 ring-cyan-400' : ''"
                                        @click="active = idx">
                                    <img :src="img.url" :alt="img.alt" class="h-20 w-full object-cover" />
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Info + Variants --}}
            <section class="lg:col-span-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold text-cyan-300">{{ $kategoriNama ?? '-' }}</div>
                            <h1 class="text-2xl md:text-3xl font-black">{{ $produk->nama_produk }}</h1>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-slate-300">Rating</div>
                            <div class="text-yellow-300 font-bold">★ {{ number_format((float)($produk->rata_rating ?? 0),1) }}</div>
                            <div class="text-xs text-slate-300">{{ $produk->jumlah_ulasan ?? 0 }} ulasan</div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="text-sm text-slate-300">Harga Dasar</div>
                        <div class="text-2xl font-black">Rp {{ number_format((int)($produk->harga_dasar ?? 0),0,',','.') }}</div>
                    </div>

                    <div class="mt-6" x-data="variantPicker({{ $variantsJson ?? '[]' }})">
                        {{-- color swatches --}}
                        <div class="mb-4">
                            <div class="text-sm font-semibold mb-2">Warna</div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="w in warnaList" :key="w.warna_id">
                                    <button type="button"
                                            class="w-9 h-9 rounded-full border border-white/10"
                                            :style="`background-color:${w.kode_hex}`"
                                            @click="selectWarna(w.warna_id)"
                                            :class="w.warna_id===selectedWarna ? 'ring-2 ring-cyan-400' : ''"></button>
                                </template>
                            </div>
                        </div>

                        {{-- size options --}}
                        <div class="mb-4">
                            <div class="text-sm font-semibold mb-2">Ukuran</div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="u in ukuranList" :key="u">
                                    <button type="button"
                                            class="rounded-full border border-white/10 px-4 py-2 text-sm hover:bg-white/5"
                                            @click="selectUkuran(u)"
                                            :class="u===selectedUkuran ? 'bg-cyan-500/20 border-cyan-400 text-cyan-200' : ''">
                                        <span x-text="u"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- selected price / stock --}}
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-slate-300">Harga</div>
                                    <div class="text-xl font-black" x-text="formatRupiah(hargaTerpilih)" ></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-slate-300">Stok</div>
                                    <div class="text-lg font-bold text-emerald-300" x-text="stokTerpilih"></div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="detail_produk_id" :value="detailIdTerpilih" />

                        {{-- qty --}}
                        <div class="mt-4 flex items-center gap-3">
                            <div class="text-sm font-semibold">Qty</div>
                            <div class="inline-flex items-center rounded-2xl border border-white/10 overflow-hidden">
                                <button type="button" class="px-4 py-2 hover:bg-white/5" @click="decQty()">-</button>
                                <input type="number" class="w-20 text-center bg-transparent outline-none px-2 py-2" x-model.number="qty" @input="clampQty()" />
                                <button type="button" class="px-4 py-2 hover:bg-white/5" @click="incQty()">+</button>
                            </div>
                            <div class="text-xs text-slate-400">max <span x-text="stokTerpilih"></span></div>
                        </div>

                        <div class="mt-5 flex flex-col sm:flex-row gap-3">
                            <button type="button" class="rounded-2xl bg-cyan-500 px-5 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400 disabled:opacity-50" 
                                    :disabled="!detailIdTerpilih"
                                    @click="addToCart()">
                                Tambah ke Keranjang
                            </button>

                            <button type="button" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-bold hover:bg-white/5"
                                    @click="toggleWishlist()">
                                ♡ Wishlist
                            </button>
                        </div>

                        <div class="mt-4 text-sm text-slate-300" x-show="error" x-text="error" ></div>
                    </div>

                    <div class="mt-6">
                        <div class="text-sm font-semibold mb-2">Deskripsi</div>
                        <div class="text-slate-300 leading-relaxed">{!! $produk->deskripsi !!}</div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Reviews (simplified for now) --}}
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black">Ulasan Produk</h2>
                    <p class="text-slate-300 text-sm mt-1">Total {{ $reviewsTotal ?? ($produk->jumlah_ulasan ?? 0) }} ulasan</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse($reviews ?? [] as $r)
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $r->foto_profile ?? 'https://via.placeholder.com/80' }}" class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <div class="font-semibold">{{ $r->nama_pengguna ?? 'Buyer' }}</div>
                                <div class="text-xs text-slate-300">{{ $r->created_at?->format('d M Y') ?? '' }}</div>
                            </div>
                            <div class="ml-auto text-yellow-300 font-bold">{{ $r->bintang }}★</div>
                        </div>
                        <div class="mt-2 text-sm">{{ $r->judul_ulasan ?? '' }}</div>
                        <div class="mt-2 text-slate-300 text-sm">{{ $r->isi_ulasan ?? '' }}</div>
                    </div>
                @empty
                    <div class="text-slate-300 text-sm">Belum ada ulasan.</div>
                @endforelse
            </div>
        </section>

    @endif
</div>

<script>
    function imageGallery(images) {
        return {
            images: images,
            active: 0,
            current() { return this.images[this.active] || { url: '', alt: '' }; }
        }
    }

    function variantPicker(variants) {
        return {
            variants: variants,
            warnaList: [],
            ukuranList: [],
            selectedWarna: null,
            selectedUkuran: null,
            detailIdTerpilih: null,
            hargaTerpilih: 0,
            stokTerpilih: 0,
            qty: 1,
            error: '',

            init() {
                this.warnaList = [...new Map(variants.map(v => [v.warna_id, v])).values()].map(v => ({ warna_id: v.warna_id, kode_hex: v.kode_hex }));
                if (this.warnaList.length) this.selectWarna(this.warnaList[0].warna_id);
            },

            selectWarna(warnaId) {
                this.selectedWarna = warnaId;
                const ukuran = [...new Set(this.variants.filter(v => String(v.warna_id) === String(warnaId)).map(v => v.ukuran))];
                this.ukuranList = ukuran;
                this.selectedUkuran = null;
                this.detailIdTerpilih = null;
                this.hargaTerpilih = 0;
                this.stokTerpilih = 0;
                this.qty = 1;
            },

            selectUkuran(ukuran) {
                this.selectedUkuran = ukuran;
                const match = this.variants.find(v => String(v.warna_id) === String(this.selectedWarna) && String(v.ukuran) === String(ukuran));
                if (match) {
                    this.detailIdTerpilih = match.detail_produk_id;
                    this.hargaTerpilih = Number(match.harga);
                    this.stokTerpilih = Number(match.stok);
                    this.qty = 1;
                    this.error = '';
                }
            },

            clampQty() {
                if (this.qty < 1) this.qty = 1;
                if (this.stokTerpilih && this.qty > this.stokTerpilih) this.qty = this.stokTerpilih;
            },
            incQty() { this.qty = this.qty + 1; this.clampQty(); },
            decQty() { this.qty = this.qty - 1; this.clampQty(); },

            async addToCart() {
                if (!this.detailIdTerpilih) return;
                try {
                    this.error = '';
                    const resp = await fetch('{{ url('/keranjang') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: new URLSearchParams({ detail_produk_id: this.detailIdTerpilih, jumlah: this.qty })
                    });
                    if (!resp.ok) throw new Error('Gagal menambah ke keranjang');
                    window.location.href='{{ route('cart.index') }}';
                } catch (e) {
                    this.error = e.message;
                }
            },

            async toggleWishlist() {
                // spec: POST /wishlist requires login; versi awal redirect ke login jika guest
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const resp = await fetch('{{ url('/wishlist') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: new URLSearchParams({ produk_id: {{ $produk->produk_id ?? 0 }} })
                    });
                    if (resp.status === 302) window.location.href='{{ route('login') }}';
                    else window.location.reload();
                } catch (e) {}
            },

            formatRupiah(v) {
                const n = Number(v || 0);
                return 'Rp ' + n.toLocaleString('id-ID');
            }
        }
    }
</script>
@endsection


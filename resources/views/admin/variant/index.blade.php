@extends('layouts.admin')

@section('title', 'Variant Management')

@section('content')
<div style="padding: 28px 28px 40px;" x-data="variantMgmt()" x-init="init()">
    
    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 24px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 4px;">Produk</p>
            <h1 class="page-header-title" style="margin: 0;">Variant Management</h1>
            <p class="page-header-sub" style="margin: 0; color: #94A3B8;">Kelola varian produk — klik "All Varian" untuk melihat dan menambah varian per produk.</p>
        </div>
    </div>

    {{-- Product Table --}}
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Daftar Produk</h2>
            <span class="text-xs text-slate-400" style="font-weight: 600;">{{ $products->count() }} produk aktif</span>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Harga Dasar</th>
                        <th>Jumlah Varian</th>
                        <th style="text-align: right; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $varianCount = $product->detailProduk ? $product->detailProduk->count() : 0;
                        @endphp
                        <tr data-produk-id="{{ $product->produk_id }}" data-produk-name="{{ addslashes($product->nama_produk) }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($product->gambarUtama)
                                        <img src="{{ $product->gambarUtama->url_safe ?? asset('images/placeholder.png') }}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover; background: #F8FAFC; border: 1px solid #E2E8F0;" alt="{{ $product->nama_produk }}">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #F1F5F9; color: #CBD5E1; display: flex; align-items: center; justify-content: center;">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                    @endif
                                    <div style="min-width: 0;">
                                        <p style="font-weight: 700; color: #0F172A; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;" title="{{ $product->nama_produk }}">{{ $product->nama_produk }}</p>
                                        <p style="font-size: 11px; color: #94A3B8; font-family: monospace; margin: 2px 0 0;">{{ $product->formatted_id ?? '#'.$product->produk_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $product->supplier->nama_toko ?? '-' }}</td>
                            <td style="font-weight: 700; color: #0F172A;">Rp {{ number_format($product->harga_dasar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $varianCount > 0 ? 'badge-admin' : '' }}" style="{{ $varianCount == 0 ? 'background:#F1F5F9; color:#64748B;' : '' }}">
                                    {{ $varianCount }} varian
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button type="button"
                                        @click="openVariantPanel({{ $product->produk_id }}, '{{ addslashes($product->nama_produk) }}')"
                                        class="btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 8px;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
                                    </svg>
                                    All Varian
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94A3B8; padding: 48px;">
                                <svg width="40" height="40" fill="none" stroke="#CBD5E1" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p style="font-size: 14px; font-weight: 500; margin: 0;">Belum ada produk aktif.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Variant Panel Slide-Over --}}
    <div x-show="panelOpen"
         x-cloak
         @click.self="closePanel()"
         class="fixed inset-0 bg-black/50 z-50 flex justify-end"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="w-full max-w-2xl h-full bg-white shadow-2xl flex flex-col overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            {{-- Panel Header --}}
            <div class="bg-gradient-to-r from-admin to-admin-dark px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Variant Management</p>
                        <h2 class="font-bold text-white text-lg" x-text="activeProdukName"></h2>
                    </div>
                    <button @click="closePanel()" class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                {{-- Existing Variants List --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-800">Varian Tersimpan</h3>
                        <span class="bg-admin/10 text-admin text-xs font-bold px-2.5 py-1 rounded-full" x-text="variants.length + ' varian'"></span>
                    </div>

                    <div x-show="loading" class="text-center py-8 text-slate-400">
                        <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-admin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-sm">Memuat varian...</p>
                    </div>

                    <div x-show="!loading && variants.length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 py-8 text-center text-sm text-slate-400">
                        Belum ada varian untuk produk ini. Tambahkan menggunakan form di bawah.
                    </div>

                    <div x-show="!loading && variants.length > 0" class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-400">
                                    <th class="px-3 py-2 font-bold">Warna</th>
                                    <th class="px-3 py-2 font-bold">Ukuran</th>
                                    <th class="px-3 py-2 font-bold">Stok</th>
                                    <th class="px-3 py-2 font-bold">Harga</th>
                                    <th class="px-3 py-2 font-bold">SKU</th>
                                    <th class="px-3 py-2 font-bold"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="variant in variants" :key="variant.id">
                                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="h-5 w-5 rounded-full border border-slate-200 shadow-sm" :style="'background:' + variant.kode_hex"></span>
                                                <span class="font-medium text-slate-700" x-text="variant.warna"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-xs text-slate-600" x-text="variant.ukuran"></span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600" x-text="variant.stok"></td>
                                        <td class="px-3 py-3 font-semibold text-slate-700" x-text="'Rp ' + Number(variant.harga || 0).toLocaleString('id-ID')"></td>
                                        <td class="px-3 py-3 font-mono text-xs text-slate-400" x-text="variant.sku"></td>
                                        <td class="px-3 py-3">
                                            <button type="button"
                                                    @click="deleteVariant(variant.id)"
                                                    class="rounded-lg bg-red-50 px-2.5 py-2 text-red-500 transition hover:bg-red-100"
                                                    aria-label="Hapus varian">✕</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Add New Variant Form --}}
                <div class="border-t border-slate-200 pt-5">
                    <h3 class="font-bold text-slate-800 mb-4">Tambah Varian Baru</h3>

                    <div x-show="errors.duplikat" x-cloak class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600">
                        <p x-text="errors.duplikat"></p>
                    </div>

                    <form @submit.prevent="saveVariant" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Warna *</label>
                                <input id="input-warna"
                                       type="text"
                                       x-model="form.warna"
                                       @input="autoSku(); checkDuplicate()"
                                       @blur="checkDuplicate()"
                                       placeholder="Black, Navy Blue, Off White"
                                       class="w-full rounded-xl border-2 px-4 py-3 text-sm focus:border-admin focus:outline-none"
                                       :class="errors.warna ? 'border-red-300' : 'border-slate-200'">
                                <p x-show="errors.warna" x-cloak class="mt-1 text-xs text-red-500" x-text="errors.warna"></p>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kode Warna</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" x-model="form.kode_hex" class="h-[46px] w-12 cursor-pointer rounded-xl border-2 border-slate-200 p-1">
                                    <input type="text" x-model="form.kode_hex" class="flex-1 rounded-xl border-2 border-slate-200 px-3 py-3 font-mono text-xs focus:border-admin focus:outline-none" placeholder="#000000">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Ukuran *</label>
                            <div class="mb-2">
                                {{-- Pilih ukuran (multi-select) --}}
                                <div class="mb-2 flex flex-wrap gap-2">
                                    @foreach(['XS','S','M','L','XL','XXL','XXXL','38','39','40','41','42','43','44','OS','FREE SIZE'] as $size)
                                        <button type="button"
                                                @click="toggleSize('{{ $size }}')"
                                                :class="form.ukuran.includes('{{ $size }}') ? 'border-admin bg-admin text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-admin'"
                                                class="rounded-lg border-2 px-3 py-1.5 text-xs font-semibold transition">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="mb-2">
                                    <input type="text"
                                           x-model="manualUkuran"
                                           @keydown.enter.prevent="addManualUkuran()"
                                           placeholder="Ketik ukuran lalu tekan Enter, atau pisahkan dengan koma"
                                           class="w-full rounded-xl border-2 px-4 py-3 text-sm focus:border-admin focus:outline-none"
                                           :class="errors.ukuran ? 'border-red-300' : 'border-slate-200'">
                                    <p class="text-xs text-slate-400 mt-1">Selected: <span x-text="form.ukuran.join(', ') || '-'"></span></p>
                                </div>
                                <input type="hidden" name="ukuran" :value="form.ukuran.join(',')">
                            </div>
                                <div class="mb-2 flex flex-wrap gap-2">
                                    @foreach(['XS','S','M','L','XL','XXL','XXXL','38','39','40','41','42','43','44','OS','FREE SIZE'] as $size)
                                        <button type="button"
                                                @click="toggleSize('{{ $size }}')"
                                                :class="form.ukuran.includes('{{ $size }}') ? 'border-admin bg-admin text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-admin'"
                                                class="rounded-lg border-2 px-3 py-1.5 text-xs font-semibold transition">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="mb-2">
                                    <input type="text"
                                           x-model="manualUkuran"
                                           @keydown.enter.prevent="addManualUkuran()"
                                           placeholder="Ketik ukuran lalu tekan Enter, atau pisahkan dengan koma"
                                           class="w-full rounded-xl border-2 px-4 py-3 text-sm focus:border-admin focus:outline-none"
                                           :class="errors.ukuran ? 'border-red-300' : 'border-slate-200'">
                                    <p class="text-xs text-slate-400 mt-1">Selected: <span x-text="form.ukuran.join(', ') || '-'"></span></p>
                                </div>
                                <input type="hidden" name="ukuran" :value="form.ukuran.join(',')">
                            <p x-show="errors.ukuran" x-cloak class="mt-1 text-xs text-red-500" x-text="errors.ukuran"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Stok Awal *</label>
                                <input type="number" min="0" x-model.number="form.stok"
                                       class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:border-admin focus:outline-none" placeholder="0">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-400">Rp</span>
                                    <input type="number" min="0" x-model.number="form.harga"
                                           class="w-full rounded-xl border-2 border-slate-200 py-3 pl-10 pr-4 text-sm focus:border-admin focus:outline-none" placeholder="0">
                                </div>
                                <p x-show="errors.harga" x-cloak class="mt-1 text-xs text-red-500" x-text="errors.harga"></p>
                            </div>
                        </div>

                        <div x-show="errors.server" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600">
                            <p x-text="errors.server"></p>
                        </div>

                        <button type="submit"
                                :disabled="saving || !!errors.duplikat"
                                :class="(saving || !!errors.duplikat) ? 'cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-admin text-white hover:bg-admin-dark'"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition">
                            <svg x-show="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="saving ? 'Menyimpan...' : '💾 Save Varian'"></span>
                        </button>

                        <div x-show="savedCount > 0" x-cloak class="text-center text-sm font-semibold text-green-600">
                            ✅ <span x-text="savedCount + ' varian disimpan'"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function variantMgmt() {
    return {
        // Panel state
        panelOpen: false,
        activeProdukId: null,
        activeProdukName: '',
        loading: false,

        // Variant list
        variants: [],
        savedCount: 0,

        // Form state
        form: {
            warna: '',
            kode_hex: '#000000',
            ukuran: [],
            stok: 0,
            harga: 0,
            sku: '',
            stok_minimum: 5,
        },
        manualUkuran: '',
        saving: false,
        errors: {},

        init() {
            // Check if URL has produk_id param to auto-open
            const params = new URLSearchParams(window.location.search);
            const produkId = params.get('produk_id');
            if (produkId) {
                // find product name from available data
                const row = document.querySelector(`[data-produk-id="${produkId}"]`);
                const name = row ? row.dataset.produkName : 'Produk';
                this.openVariantPanel(produkId, name);
            }
        },

        async openVariantPanel(produkId, produkName) {
            this.activeProdukId = produkId;
            this.activeProdukName = produkName;
            this.panelOpen = true;
            this.variants = [];
            this.savedCount = 0;
            this.resetForm();
            await this.fetchVariants();
        },

        closePanel() {
            this.panelOpen = false;
            this.activeProdukId = null;
            this.variants = [];
        },

        async fetchVariants() {
            if (!this.activeProdukId) return;
            this.loading = true;
            try {
                const resp = await fetch(`{{ url('/admin/variant') }}?produk_id=${this.activeProdukId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await resp.json().catch(() => ({}));
                if (data.variants) {
                    this.variants = data.variants;
                } else if (Array.isArray(data)) {
                    this.variants = data;
                }
            } catch (e) {
                console.error('Failed to fetch variants', e);
            } finally {
                this.loading = false;
            }
        },

        autoSku() {
            const warna = this.form.warna.substring(0, 3).toUpperCase().replace(/\s+/g, '');
            const ukuran = Array.isArray(this.form.ukuran) ? (this.form.ukuran[0] || '') : (this.form.ukuran || '');
            const ukuranSlug = String(ukuran).toUpperCase().replace(/\s+/g, '');
            this.form.sku = 'SKU-' + (this.activeProdukId || '000') + '-' + (warna || 'VRN') + '-' + (ukuranSlug || 'SZ');
        },

        isDuplicateFor(ukuran) {
            const warna = this.form.warna.trim().toLowerCase();
            if (!warna || !ukuran) return false;
            return this.variants.some(v =>
                String(v.warna || '').toLowerCase() === warna &&
                String(v.ukuran || '').toLowerCase() === String(ukuran).toLowerCase()
            );
        },

        checkDuplicate() {
            this.errors.duplikat = '';
            if (!this.form.warna.trim() || this.form.ukuran.length === 0) return;
            const duplicates = this.form.ukuran.filter(u => this.isDuplicateFor(u));
            if (duplicates.length) {
                this.errors.duplikat = 'Kombinasi warna ' + this.form.warna + ' + ukuran ' + duplicates.join(', ') + ' sudah ada!';
            }
        },

        toggleSize(size) {
            const idx = this.form.ukuran.indexOf(size);
            if (idx === -1) this.form.ukuran.push(size);
            else this.form.ukuran.splice(idx, 1);
            this.autoSku();
            this.checkDuplicate();
        },

        addManualUkuran() {
            if (!this.manualUkuran) return;
            const parts = this.manualUkuran.split(',').map(s => s.trim()).filter(Boolean);
            parts.forEach(p => { if (!this.form.ukuran.includes(p)) this.form.ukuran.push(p); });
            this.manualUkuran = '';
            this.autoSku();
            this.checkDuplicate();
        },

        validateForm() {
            this.errors = {};
            if (!this.form.warna || !this.form.warna.trim()) { this.errors.warna = 'Nama warna wajib diisi'; }
            if (!Array.isArray(this.form.ukuran) || this.form.ukuran.length === 0) { this.errors.ukuran = 'Ukuran wajib dipilih minimal satu'; }
            if (Number(this.form.stok) < 0) { this.errors.stok = 'Stok tidak boleh negatif'; }
            if (Number(this.form.harga) <= 0) { this.errors.harga = 'Harga wajib diisi'; }
            this.checkDuplicate();
            return Object.keys(this.errors).length === 0;
        },

        resetForm() {
            this.form = { warna: '', kode_hex: '#000000', ukuran: [], stok: 0, harga: 0, sku: '', stok_minimum: 5 };
            this.manualUkuran = '';
            this.errors = {};
        },

        async saveVariant() {
            if (!this.validateForm()) return;
            this.saving = true;
            this.errors.server = '';
            try {
                const response = await fetch('{{ route('admin.variant.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                            body: JSON.stringify({
                                produk_id: this.activeProdukId,
                                warna: this.form.warna,
                                kode_hex: this.form.kode_hex,
                                ukuran: this.form.ukuran,
                                stok: this.form.stok,
                                harga: this.form.harga,
                                sku: this.form.sku,
                                stok_minimum: this.form.stok_minimum,
                            }),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    this.errors.server = data.message ?? (Array.isArray(data.errors) ? data.errors.join('\n') : 'Gagal menyimpan varian');
                    return;
                }

                if (Array.isArray(data.variants) && data.variants.length > 0) {
                    // add each created variant to list
                    data.variants.forEach(v => this.variants.unshift(v));
                    this.savedCount += data.variants.length;
                }

                if (Array.isArray(data.errors) && data.errors.length) {
                    this.errors.server = data.errors.join('\n');
                }

                this.resetForm();
                if (typeof showAdminToast === 'function') showAdminToast((data.variants?.length || 0) + ' varian berhasil disimpan!');
                setTimeout(() => document.getElementById('input-warna')?.focus(), 100);
            } catch (e) {
                this.errors.server = 'Terjadi kesalahan: ' + e.message;
            } finally {
                this.saving = false;
            }
        },

        async deleteVariant(id) {
            if (!confirm('Hapus varian ini?')) return;
            try {
                const response = await fetch('{{ url('/admin/variant') }}/' + id, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                });
                const data = await response.json().catch(() => ({}));
                if (response.ok && data.success) {
                    this.variants = this.variants.filter(v => v.id !== id);
                    if (typeof showAdminToast === 'function') showAdminToast('Varian dihapus');
                } else {
                    if (typeof showAdminToast === 'function') showAdminToast(data.message ?? 'Gagal menghapus varian', 'error');
                }
            } catch (e) {
                alert('Gagal hapus: ' + e.message);
            }
        },
    };
}
</script>
@endsection

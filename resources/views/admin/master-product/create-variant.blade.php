@extends('layouts.admin')

@section('title', 'Add Product — Step 2 Variants')

@section('content')
@php($step1Slug = data_get(session('product_step1', []), 'slug', '000'))
<div class="mx-auto max-w-4xl px-4 py-6">
    {{-- Step Indicator --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="font-semibold text-gray-600 text-sm">General Info</span>
        </div>
        <div class="flex-1 h-0.5 bg-admin"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-admin text-white flex items-center justify-center text-sm font-bold">2</span>
            <span class="font-bold text-admin">Variants</span>
        </div>
        <div class="flex-1 h-0.5 bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold">3</span>
            <span class="text-gray-400 font-semibold text-sm">Media</span>
        </div>
    </div>

    <div x-data="{
        variants: @json(session('product_step2', [])),
        form: { warna: '', kode_hex: '#000000', ukuran: [], stok: 0, harga: 0, sku: '', stok_minimum: 5, gambar_base64: '' },
        customSize: '',
        saving: false,
        errors: {},
        savedCount: @json(count(session('product_step2', []))),
        isDuplicate() {
            return this.form.ukuran.some(sz =>
                this.variants.some(v =>
                    String(v.nama_warna || v.warna || '').toLowerCase() === String(this.form.warna || '').toLowerCase() &&
                    String(v.ukuran || '').toLowerCase() === String(sz || '').toLowerCase()
                )
            );
        },
        toggleUkuran(sz) {
            if (this.form.ukuran.includes(sz)) {
                this.form.ukuran = this.form.ukuran.filter(x => x !== sz);
            } else {
                this.form.ukuran.push(sz);
            }
            this.errors.ukuran = '';
            this.errors.duplikat = '';
            this.autoSku();
        },
        addCustomSize() {
            const sz = String(this.customSize).trim();
            if (sz && !this.form.ukuran.includes(sz)) {
                this.form.ukuran.push(sz);
                this.customSize = '';
                this.errors.ukuran = '';
                this.errors.duplikat = '';
                this.autoSku();
            }
        },
        autoSku() {
            const warna = String(this.form.warna || '').substring(0, 3).toUpperCase();
            if (this.form.ukuran.length > 0) {
                const sizesStr = this.form.ukuran.join(', ');
                this.form.sku = 'SKU-{{ $step1Slug }}-' + (warna || 'VRN') + '-[' + sizesStr + ']';
            } else {
                this.form.sku = 'SKU-{{ $step1Slug }}-' + (warna || 'VRN') + '-SZ';
            }
        },
        resetForm() {
            this.form = { warna: '', kode_hex: '#000000', ukuran: [], stok: 0, harga: 0, sku: '', stok_minimum: 5, gambar_base64: '' };
            this.customSize = '';
            this.errors = {};
            const fileInput = document.getElementById('file-input-variant');
            if (fileInput) fileInput.value = '';
        },
        validateForm() {
            this.errors = {};
            if (!String(this.form.warna).trim()) { this.errors.warna = 'Warna wajib diisi'; return false; }
            if (this.form.ukuran.length === 0) { this.errors.ukuran = 'Pilih minimal satu ukuran'; return false; }
            if (Number(this.form.harga) <= 0) { this.errors.harga = 'Harga wajib diisi'; return false; }
            if (this.isDuplicate()) { this.errors.duplikat = 'Salah satu kombinasi warna + ukuran yang dipilih sudah ada!'; return false; }
            return true;
        },
        saveVariant() {
            if (!this.validateForm()) return;
            
            this.form.ukuran.forEach(sz => {
                const warnaStr = String(this.form.warna || '').substring(0, 3).toUpperCase();
                const skuForSize = 'SKU-{{ $step1Slug }}-' + (warnaStr || 'VRN') + '-' + String(sz).toUpperCase();
                
                this.variants.push({
                    nama_variant: this.form.warna,
                    nama_warna: this.form.warna,
                    kode_hex: this.form.kode_hex,
                    ukuran: sz,
                    stok_awal: this.form.stok,
                    stok_minimum: this.form.stok_minimum,
                    price_adjustment: this.form.harga,
                    is_active: '1',
                    sku_preview: skuForSize,
                    gambar_base64: this.form.gambar_base64 || '',
                });
            });
            
            this.savedCount = this.variants.length;
            this.resetForm();
            this.$nextTick(() => document.getElementById('input-warna-step2')?.focus());
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
            this.savedCount = this.variants.length;
        },
    }">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- LEFT: Saved Variants List --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm sticky top-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Varian Tersimpan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar varian yang akan ditambahkan</p>
                        </div>
                        <span class="bg-admin/10 text-admin text-xs font-bold px-2.5 py-1 rounded-full" x-text="variants.length + ' varian'"></span>
                    </div>

                    <div x-show="variants.length === 0" class="text-center py-8 text-gray-400">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl mx-auto mb-3 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium">Belum ada varian</p>
                        <p class="text-xs text-gray-300 mt-1">Isi form di sebelah kanan, lalu klik Save</p>
                    </div>

                    <div x-show="variants.length > 0" class="space-y-2 max-h-80 overflow-y-auto pr-1">
                        <template x-for="(v, idx) in variants" :key="idx">
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl group">
                                <div class="flex-shrink-0 flex items-center justify-center">
                                    <template x-if="v.gambar_base64">
                                        <img :src="v.gambar_base64" class="w-8 h-8 rounded-lg object-cover border border-gray-200 shadow-sm">
                                    </template>
                                    <template x-if="!v.gambar_base64">
                                        <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm" :style="'background:' + (v.kode_hex || '#000000')"></div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="(v.nama_warna || '-') + ' / ' + (v.ukuran || '-')"></p>
                                    <p class="text-[10px] text-gray-400" x-text="'Stok: ' + (v.stok_awal ?? 0) + ' · Rp ' + Number(v.price_adjustment ?? 0).toLocaleString('id-ID')"></p>
                                </div>
                                <button type="button" @click="removeVariant(idx)" class="w-6 h-6 rounded-lg bg-red-50 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-red-100 transition flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <p x-show="savedCount > 0" x-cloak class="text-center text-xs text-green-600 font-semibold mt-3 py-2 bg-green-50 rounded-xl">
                        ✅ <span x-text="savedCount"></span> varian siap disimpan
                    </p>
                </div>
            </div>

            {{-- RIGHT: Add Variant Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="bg-gradient-to-r from-admin to-admin-dark px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white font-bold text-lg">＋</div>
                            <div>
                                <p class="font-bold text-white">Tambah Varian Baru</p>
                                <p class="text-white/75 text-xs mt-0.5">Klik <strong>Save Varian</strong> untuk tambah ke list. Klik <strong>Next →</strong> jika sudah selesai.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Duplicate Error --}}
                        <div x-show="errors.duplikat" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-red-600 text-xs font-medium" x-text="errors.duplikat"></p>
                        </div>

                        {{-- Warna --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Warna *</label>
                                <input id="input-warna-step2"
                                       type="text"
                                       x-model="form.warna"
                                       @input="autoSku(); errors.warna=''; errors.duplikat=''"
                                       placeholder="Black, Navy, Off White..."
                                       :class="errors.warna ? 'border-red-300' : 'border-gray-200 focus:border-admin'"
                                       class="w-full px-3 py-2.5 border-2 rounded-xl text-sm focus:outline-none transition">
                                <p x-show="errors.warna" x-cloak x-text="errors.warna" class="text-red-500 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kode Warna</label>
                                <div class="flex gap-1.5">
                                    <input type="color" x-model="form.kode_hex" class="w-10 h-[42px] rounded-xl border-2 border-gray-200 cursor-pointer p-1 flex-shrink-0">
                                    <input type="text" x-model="form.kode_hex" class="flex-1 min-w-0 px-2 py-2.5 border-2 border-gray-200 focus:border-admin rounded-xl text-xs font-mono focus:outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Ukuran --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ukuran * (Bisa pilih lebih dari 1)</label>
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @foreach($ukurans as $sz)
                                    <button type="button"
                                            @click="toggleUkuran('{{ $sz }}')"
                                            :class="form.ukuran.includes('{{ $sz }}') ? 'bg-admin text-white border-admin' : 'bg-white text-gray-600 border-gray-200 hover:border-admin'"
                                            class="px-2.5 py-1 rounded-lg border-2 text-xs font-semibold transition">{{ $sz }}</button>
                                @endforeach
                            </div>
                            
                            {{-- Selected sizes badges --}}
                            <div x-show="form.ukuran.length > 0" x-cloak class="flex flex-wrap gap-1 mb-2 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                <span class="text-xs text-gray-400 w-full mb-1">Ukuran terpilih:</span>
                                <template x-for="sz in form.ukuran" :key="sz">
                                    <span class="inline-flex items-center gap-1 bg-admin/10 text-admin text-xs font-bold px-2.5 py-1 rounded-lg">
                                        <span x-text="sz"></span>
                                        <button type="button" @click="toggleUkuran(sz)" class="hover:text-red-500 font-bold ml-0.5">×</button>
                                    </span>
                                </template>
                            </div>

                            <div class="flex gap-2">
                                <input type="text"
                                       x-model="customSize"
                                       @keydown.enter.prevent="addCustomSize()"
                                       placeholder="Ketik ukuran custom..."
                                       class="flex-1 px-3 py-2 border-2 border-gray-200 focus:border-admin rounded-xl text-sm focus:outline-none transition">
                                <button type="button" @click="addCustomSize()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-200 border-2 border-gray-200 transition">Tambah</button>
                            </div>
                            <p x-show="errors.ukuran" x-cloak x-text="errors.ukuran" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        {{-- Stok & Harga --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Stok Awal *</label>
                                <input type="number" x-model.number="form.stok" min="0"
                                       class="w-full px-3 py-2.5 border-2 border-gray-200 focus:border-admin rounded-xl text-sm focus:outline-none transition"
                                       placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Stok Minimum</label>
                                <input type="number" x-model.number="form.stok_minimum" min="1"
                                       class="w-full px-3 py-2.5 border-2 border-gray-200 focus:border-admin rounded-xl text-sm focus:outline-none transition"
                                       placeholder="5">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Harga Jual *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                                <input type="number"
                                       x-model.number="form.harga"
                                       min="0"
                                       :class="errors.harga ? 'border-red-300' : 'border-gray-200 focus:border-admin'"
                                       class="w-full pl-9 pr-3 py-2.5 border-2 rounded-xl text-sm focus:outline-none transition"
                                       placeholder="0">
                            </div>
                            <p x-show="errors.harga" x-cloak x-text="errors.harga" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Foto Varian (Optional)</label>
                            <div class="flex items-center gap-3">
                                <input type="file"
                                       accept="image/*"
                                       @change="
                                           const file = $event.target.files[0];
                                           if (file) {
                                               const reader = new FileReader();
                                               reader.onload = (e) => { form.gambar_base64 = e.target.result; };
                                               reader.readAsDataURL(file);
                                           } else {
                                               form.gambar_base64 = '';
                                           }
                                       "
                                       class="hidden"
                                       id="file-input-variant">
                                <button type="button"
                                        @click="document.getElementById('file-input-variant').click()"
                                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold border-2 border-gray-200 transition">
                                    Pilih Foto
                                </button>
                                <template x-if="form.gambar_base64">
                                    <div class="relative w-12 h-12 rounded-xl overflow-hidden border-2 border-gray-200">
                                        <img :src="form.gambar_base64" class="w-full h-full object-cover">
                                        <button type="button"
                                                @click="form.gambar_base64 = ''; document.getElementById('file-input-variant').value = '';"
                                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] hover:bg-red-600 transition">
                                            ×
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">SKU (Auto-generated)</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="form.sku"
                                       class="flex-1 px-3 py-2.5 border-2 border-gray-200 focus:border-admin rounded-xl text-sm font-mono bg-gray-50 focus:outline-none transition">
                                <button type="button" @click="autoSku()" class="px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-200 transition">Auto</button>
                            </div>
                        </div>

                        {{-- ACTIONS: Save + Next as separate clear buttons --}}
                        <div class="pt-2 border-t border-gray-100">
                            {{-- Save Varian button --}}
                            <button type="button"
                                    @click="saveVariant()"
                                    :disabled="!!errors.duplikat"
                                    :class="!!errors.duplikat ? 'opacity-60 cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-admin text-white hover:bg-admin-dark hover:shadow-lg hover:shadow-admin/30'"
                                    class="w-full py-3.5 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                💾 Save Varian
                            </button>

                            <p class="text-center text-xs text-gray-400 mb-3">Setelah klik Save, form akan kosong otomatis untuk varian berikutnya.</p>

                            {{-- Next button (submit ke step 3) --}}
                            <form action="{{ route('admin.master-product.variant.store') }}" method="POST">
                                @csrf
                                <template x-for="(v, idx) in variants" :key="'hidden-' + idx">
                                    <div>
                                        <input type="hidden" :name="`variants[${idx}][nama_variant]`" :value="v.nama_variant">
                                        <input type="hidden" :name="`variants[${idx}][ukuran]`" :value="v.ukuran">
                                        <input type="hidden" :name="`variants[${idx}][nama_warna]`" :value="v.nama_warna">
                                        <input type="hidden" :name="`variants[${idx}][kode_hex]`" :value="v.kode_hex">
                                        <input type="hidden" :name="`variants[${idx}][stok_awal]`" :value="v.stok_awal">
                                        <input type="hidden" :name="`variants[${idx}][stok_minimum]`" :value="v.stok_minimum">
                                        <input type="hidden" :name="`variants[${idx}][price_adjustment]`" :value="v.price_adjustment">
                                        <input type="hidden" :name="`variants[${idx}][is_active]`" :value="v.is_active">
                                        <input type="hidden" :name="`variants[${idx}][gambar_base64]`" :value="v.gambar_base64">
                                    </div>
                                </template>
                                <button type="submit"
                                        :disabled="variants.length === 0"
                                        :class="variants.length === 0 ? 'opacity-50 cursor-not-allowed bg-gray-100 text-gray-400 border-gray-200' : 'bg-white text-admin border-admin hover:bg-admin hover:text-white'"
                                        class="w-full py-3 rounded-xl font-bold text-sm border-2 transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span x-text="variants.length === 0 ? 'Simpan minimal 1 varian dulu' : 'Next → Media (' + variants.length + ' varian)'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title','Add Product — Step 2 Variants')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">✓</span>
            <span class="font-semibold text-slate-800">General Info</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-[#2B9BAF] text-white flex items-center justify-center text-sm font-bold">2</span>
            <span class="font-semibold text-[#2B9BAF]">Variants</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold">3</span>
            <span class="text-gray-500 font-semibold">Media</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-bold text-slate-900 mb-3">Ringkasan Step 1</h3>
                <div class="text-sm text-slate-700"><span class="font-semibold">Nama:</span> {{ $step1['nama_produk'] }}</div>
                <div class="text-sm text-slate-700 mt-2"><span class="font-semibold">Kategori ID:</span> {{ $step1['kategori_id'] }}</div>
                <div class="text-sm text-slate-700 mt-2"><span class="font-semibold">Harga Dasar:</span> Rp {{ number_format($step1['harga_dasar'],0,',','.') }}</div>
            </div>
        </div>

        <div class="lg:col-span-9">
            <form method="POST" action="{{ route('admin.master-product.variant.store') }}" enctype="multipart/form-data">
                @csrf
                <div x-data="{
                    variants: [{
                        id: Date.now(),
                        nama: '', ukuran: 'S',
                        nama_variant: '',
                        warna: 'Hitam', hex: '#000000',
                        stok: 0, min_stok: 5, price_adj: 0,
                        is_active: '1', sku: '',
                        kode_hex: '#000000',
                        nama_warna: 'Hitam'
                    }],
                    addVariant() {
                        this.variants.push({
                            id: Date.now(),
                            nama: '', ukuran: 'S',
                            nama_variant: '',
                            warna: 'Hitam', hex: '#000000',
                            stok: 0, min_stok: 5, price_adj: 0,
                            is_active: '1', sku: '',
                            kode_hex: '#000000',
                            nama_warna: 'Hitam'
                        });
                    },
                    removeVariant(id) {
                        if(this.variants.length > 1) {
                            this.variants = this.variants.filter(v => v.id !== id);
                        }
                    },
                    generateSKU(v, i) {
                        v.sku = 'SKU-' + String(i+1).padStart(3,'0')
                            + '-' + v.ukuran
                            + '-' + (v.nama_warna || v.warna).toLowerCase().replace(/\s+/g,'-');
                    },
                    updateColor(v, warna, hex) {
                        v.warna = warna;
                        v.hex = hex;
                        v.nama_warna = warna;
                        v.kode_hex = hex;
                    }
                }" class="space-y-5">

                    <template x-for="(v,i) in variants" :key="v.id">
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <div class="text-sm text-slate-500">Variant #<span x-text="i+1"></span></div>
                                    <div class="font-bold text-slate-900">Configure</div>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-700 text-sm" x-show="variants.length > 1" @click="removeVariant(v.id)">🗑 Remove</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-8 space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Variant Name</label>
                                        <input type="text"
                                               x-model="v.nama"
                                               @input="v.nama_variant = v.nama; generateSKU(v,i)"
                                               x-init="generateSKU(v,i)"
                                               :name="`variants[${i}][nama_variant]`"
                                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />

                                        <div class="mt-1 text-xs text-slate-400">
                                            SKU: <span x-text="v.sku"></span>
                                        </div>
                                        <input type="hidden" :name="`variants[${i}][sku_preview]`" :value="v.sku">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Size</label>
                                            <select x-model="v.ukuran" @change="generateSKU(v,i)" :name="`variants[${i}][ukuran]`" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">
                                                @foreach($ukurans as $u)
                                                    <option value="{{ $u }}">{{ $u }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Color</label>
                                            <select
                                                :name="`variants[${i}][nama_warna]`"
                                                @change="updateColor(v,$event.target.value,$event.target.options[$event.target.selectedIndex].dataset.hex); generateSKU(v,i)"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]">
                                                @foreach($daftarWarna as $nama => $hex)
                                                    <option value="{{ $nama }}" data-hex="{{ $hex }}" :selected="v.nama_warna === '{{ $nama }}'">{{ $nama }}</option>
                                                @endforeach
                                            </select>

                                            <div class="mt-2 flex items-center gap-3">
                                                <div class="w-8 h-8 rounded border" :style="`background-color:${v.hex}`"></div>
                                                <div class="text-xs text-slate-500">Preview</div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" :name="`variants[${i}][kode_hex]`" :value="v.kode_hex">
                                    <input type="hidden" :name="`variants[${i}][nama_warna]`" x-model="v.nama_warna">

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Initial Stock</label>
                                            <input type="number" min="0" x-model="v.stok" :name="`variants[${i}][stok_awal]`" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Min Stock</label>
                                            <input type="number" min="0" x-model="v.min_stok" :name="`variants[${i}][stok_minimum]`" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Price Adjustment</label>
                                            <input type="number" x-model="v.price_adj" :name="`variants[${i}][price_adjustment]`" step="0.01" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2B9BAF]" placeholder="0" />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Visibility</label>
                                        <div class="flex items-center gap-4">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" :name="`variants[${i}][is_active]`" value="1" x-model="v.is_active" />
                                                <span class="text-sm text-slate-700">Active</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" :name="`variants[${i}][is_active]`" value="0" x-model="v.is_active" />
                                                <span class="text-sm text-slate-700">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-4 space-y-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Variant Media</label>
                                        <input type="file" :name="`variant_gambar_${i}[]`" multiple accept="image/*" class="w-full" />
                                        <div class="text-xs text-slate-500 mt-1">(Upload multiple, max terserah backend)</div>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-500">
                                        Preview gambar variant dapat ditambahkan jika diperlukan. 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addVariant()" class="border-2 border-dashed border-[#2B9BAF] text-[#2B9BAF] w-full py-3 rounded-lg font-semibold">+ Add Another Variant</button>

                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('admin.master-product.create') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Back</a>
                        <button type="submit" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Next →</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


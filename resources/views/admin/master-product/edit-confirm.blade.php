@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.master-product.detail', $produk->produk_id) }}" class="text-slate-600 hover:text-slate-900">← Back to Detail</a>
    </div>

    <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-5 mb-6 flex items-start gap-4">
        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-amber-800">⚠️ Kamu akan mengedit Master Produk</p>
            <p class="text-amber-700 text-sm mt-1">Perubahan pada master produk akan mempengaruhi semua tampilan di sisi buyer. Pastikan kamu yakin sebelum menyimpan perubahan.</p>
            <p class="text-amber-700 text-sm mt-1 font-semibold">Produk: {{ $produk->nama_produk }}</p>
        </div>
    </div>

    <form action="{{ route('admin.master-product.update', $produk->produk_id) }}" method="POST" class="space-y-5" x-data="{ confirmed: false }">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name</label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                    <select name="kategori_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        @foreach($produk->kategori ? [$produk->kategori] : [] as $kategori)
                            <option value="{{ $kategori->kategori_id }}" selected>{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        @foreach($produk->supplier ? [$produk->supplier] : [] as $supplier)
                            <option value="{{ $supplier->supplier_id }}" selected>{{ $supplier->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Base Price</label>
                    <input type="number" name="harga_dasar" min="0" value="{{ old('harga_dasar', $produk->harga_dasar) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gender</label>
                    <select name="gender" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        @foreach(['men' => 'Men', 'women' => 'Women', 'unisex' => 'Unisex', 'kids' => 'Kids'] as $value => $label)
                            <option value="{{ $value }}" {{ old('gender', $produk->gender) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Sport Type</label>
                    <input type="text" name="tipe_olahraga" value="{{ old('tipe_olahraga', $produk->tipe_olahraga) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Minimum</label>
                    <input type="number" name="stok_minimum" min="0" value="{{ old('stok_minimum', $produk->stok_minimum) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tags</label>
                    <input type="text" name="tags" value="{{ old('tags', is_array($produk->tags) ? implode(', ', $produk->tags) : '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none" placeholder="running, gym, lifestyle">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                <textarea name="deskripsi" rows="6" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Specification</label>
                <textarea name="spesifikasi" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">{{ old('spesifikasi', $produk->spesifikasi) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Publish Status</label>
                    <select name="status_publish" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        @foreach(['publish' => 'Publish', 'draft' => 'Draft', 'scheduled' => 'Scheduled'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status_publish', $produk->status_publish) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($produk->scheduled_at)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                </div>
                <div class="flex items-end gap-4 pb-1">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $produk->is_featured) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Featured</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $produk->is_active) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border-2 border-gray-200 p-5">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" x-model="confirmed" class="w-4 h-4 mt-1 accent-[#63A2BB] flex-shrink-0">
                <span class="text-sm text-gray-700">
                    <strong>Saya memahami</strong> bahwa perubahan ini akan langsung mempengaruhi tampilan produk di sisi buyer dan tidak dapat dibatalkan secara otomatis.
                </span>
            </label>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.master-product.index') }}" class="flex-1 py-3 border-2 border-gray-200 text-gray-500 rounded-xl font-bold text-sm text-center hover:bg-gray-50 transition">Batal</a>
            <button type="submit" :disabled="!confirmed" :class="!confirmed ? 'opacity-50 cursor-not-allowed bg-gray-300 text-gray-500' : 'bg-[#63A2BB] text-white hover:bg-[#4A8BA3]'" class="flex-[2] py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Edit Master Product')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.master-product.detail', $produk->produk_id) }}" class="text-slate-600 hover:text-slate-900">← Back to Detail</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $produk->formatted_id }}</div>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Edit {{ $produk->nama_produk }}</h1>
        </div>

        <form method="POST" action="{{ route('admin.master-product.update', $produk->produk_id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name</label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                    <select name="kategori_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->kategori_id }}" {{ (string) old('kategori_id', $produk->kategori_id) === (string) $kategori->kategori_id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ (string) old('supplier_id', $produk->supplier_id) === (string) $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Base Price</label>
                    <input type="number" name="harga_dasar" min="0" value="{{ old('harga_dasar', $produk->harga_dasar) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gender</label>
                    <select name="gender" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach(['men' => 'Men', 'women' => 'Women', 'unisex' => 'Unisex', 'kids' => 'Kids'] as $value => $label)
                            <option value="{{ $value }}" {{ old('gender', $produk->gender) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Sport Type</label>
                    <input type="text" name="tipe_olahraga" value="{{ old('tipe_olahraga', $produk->tipe_olahraga) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Minimum</label>
                    <input type="number" name="stok_minimum" min="0" value="{{ old('stok_minimum', $produk->stok_minimum) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tags</label>
                    <input type="text" name="tags" value="{{ old('tags', is_array($produk->tags) ? implode(', ', $produk->tags) : '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="running, gym, lifestyle">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                <textarea name="deskripsi" rows="6" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Specification</label>
                <textarea name="spesifikasi" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">{{ old('spesifikasi', $produk->spesifikasi) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Publish Status</label>
                    <select name="status_publish" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach(['publish' => 'Publish', 'draft' => 'Draft', 'scheduled' => 'Scheduled'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status_publish', $produk->status_publish) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($produk->scheduled_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
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

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('admin.master-product.detail', $produk->produk_id) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

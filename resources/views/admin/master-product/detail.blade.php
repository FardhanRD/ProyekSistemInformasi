@extends('layouts.admin')

@section('title', 'Master Product Detail')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.master-product.index') }}" class="text-slate-600 hover:text-slate-900">← Back to Master Product</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm mb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <div class="flex items-start gap-4">
                    <div class="h-16 w-16 overflow-hidden rounded-2xl bg-slate-100">
                        @if($produk->gambarUtama)
                            <img src="{{ Storage::url($produk->gambarUtama->url_gambar) }}" alt="{{ $produk->nama_produk }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-400">—</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $produk->formatted_id }}</div>
                        <h1 class="text-3xl font-bold text-slate-900 mt-1">{{ $produk->nama_produk }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                            <span>{{ $produk->kategori->nama_kategori ?? '-' }}</span>
                            <span>•</span>
                            <span>{{ $produk->supplier->nama_toko ?? '-' }}</span>
                            <span>•</span>
                            <span>{{ ucfirst($produk->gender ?? 'unisex') }}</span>
                            <span>•</span>
                            <span>{{ $produk->tipe_olahraga ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-500">Harga Dasar</div>
                        <div class="mt-2 text-xl font-bold text-slate-900">Rp {{ number_format($produk->harga_dasar ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-500">Status Stok</div>
                        <div class="mt-2 text-xl font-bold text-slate-900">{{ strtoupper($produk->status_stok) }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-500">Stock Minimum</div>
                        <div class="mt-2 text-xl font-bold text-slate-900">{{ $produk->stok_minimum ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.master-product.edit', $produk->produk_id) }}" class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">Edit Product</a>
                <a href="{{ route('product.show.alias', $produk->slug) }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Preview Buyer View</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Product Information</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-500">Description</dt><dd class="mt-1 text-slate-800 whitespace-pre-line">{{ $produk->deskripsi }}</dd></div>
                <div><dt class="text-slate-500">Specification</dt><dd class="mt-1 text-slate-800 whitespace-pre-line">{{ $produk->spesifikasi ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Tags</dt><dd class="mt-1 text-slate-800">{{ is_array($produk->tags) ? implode(', ', $produk->tags) : '-' }}</dd></div>
                <div><dt class="text-slate-500">Publish Status</dt><dd class="mt-1 text-slate-800">{{ ucfirst($produk->status_publish ?? 'draft') }}</dd></div>
            </dl>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Media</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @forelse($produk->images as $image)
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                        <img src="{{ Storage::url($image->url_gambar) }}" alt="{{ $produk->nama_produk }}" class="h-32 w-full object-cover">
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-slate-500">No images available</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="font-bold text-slate-900">Variants</h2>
                <p class="text-sm text-slate-500">{{ $produk->detailProduk->count() }} variant(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-2">Variant</th>
                        <th class="py-3 px-2">Color</th>
                        <th class="py-3 px-2">Size</th>
                        <th class="py-3 px-2">SKU</th>
                        <th class="py-3 px-2">Stock</th>
                        <th class="py-3 px-2">Price</th>
                        <th class="py-3 px-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk->detailProduk as $detail)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-2 font-medium text-slate-900">{{ $detail->nama_produk }}</td>
                            <td class="py-3 px-2">{{ $detail->warna->nama_warna ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $detail->ukuran ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $detail->sku ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $detail->stok ?? 0 }}</td>
                            <td class="py-3 px-2">Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2">
                                @if(($detail->is_active ?? 0) == 1)
                                    <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">ACTIVE</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">INACTIVE</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Media Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Media Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola gambar produk, set thumbnail, dan hapus media.</p>
        </div>

        <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">
            + Upload Media
        </button>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
            <select name="produk_id" class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Products</option>
                @foreach($produk_list as $p)
                    <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                        {{ $p->nama_produk }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Filter</button>
        </form>
    </div>

    {{-- Grid Media --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        @forelse($media as $item)
            <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition bg-white">
                <div class="relative aspect-square bg-slate-100">
                    @if($item->url_gambar && Storage::disk('public')->exists($item->url_gambar))
                        <img src="{{ Storage::url($item->url_gambar) }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    @if($item->urutan == 0)
                        <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                            THUMBNAIL
                        </div>
                    @endif
                </div>

                <div class="p-3">
                    <div class="text-xs text-slate-500 mb-2">{{ $item->produk->nama_produk ?? '-' }}</div>
                    <div class="flex items-center gap-2">
                        @if($item->urutan != 0)
                            <form method="POST" action="{{ route('admin.media.set-thumbnail', $item->gambar_id) }}" class="flex-1">
                                @csrf @method('PUT')
                                <button type="submit" class="w-full text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 font-medium">
                                    Set Thumbnail
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.media.destroy', $item->gambar_id) }}" onsubmit="return confirm('Hapus gambar ini?')" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-600">
                Belum ada media. Silakan upload gambar terlebih dahulu.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($media ?? null, 'links'))
        <div class="flex justify-center">
            {{ $media->links() }}
        </div>
    @endif
</div>

{{-- Upload Modal --}}
<div id="uploadModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Upload Media</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('uploadModal').classList.add('hidden')">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm font-semibold text-slate-700">Produk</label>
                <select name="produk_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
                    <option value="">Pilih produk...</option>
                    @foreach($produk_list as $p)
                        <option value="{{ $p->produk_id }}">{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Gambar (Max 10MB)</label>
                <input type="file" name="gambar" accept="image/*" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Alt Text (optional)</label>
                <input type="text" name="alt_text" placeholder="Deskripsi gambar" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('uploadModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Upload</button>
            </div>
        </form>
    </div>
</div>

@endsection

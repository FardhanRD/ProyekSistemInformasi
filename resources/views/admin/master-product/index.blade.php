@extends('layouts.admin')

@section('title','Master Product')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-bold text-admin uppercase tracking-widest mb-1">MOVR ADMIN</p>
            <h1 class="text-2xl font-black text-gray-900">Master Product</h1>
            <p class="text-sm text-gray-400 mt-1">Referensi produk — read-only. Gunakan "Detail" untuk lihat histori, atau buat produk baru.</p>
        </div>
        <a href="{{ route('admin.master-product.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-admin text-white rounded-xl text-sm font-bold shadow-lg shadow-admin/25 hover:bg-admin-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-admin/30 transition-all duration-200 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </a>
    </div>

    <!-- Info read-only notice -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 flex items-start gap-3">
        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-amber-800 text-sm font-bold">Master Produk bersifat Read-Only</p>
            <p class="text-amber-700 text-xs mt-0.5">Data produk master tidak dapat diubah langsung dari halaman ini. Gunakan tombol <strong>"Detail"</strong> untuk melihat informasi lengkap, atau <strong>"Varian"</strong> / <strong>"Media"</strong> untuk mengelola konten produk.</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm">
        <form method="GET" action="{{ route('admin.master-product.index') }}">
            <!-- Search Row -->
            <div class="relative mb-3">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search', $search ?? '') }}" placeholder="Cari nama produk, slug, atau SKU..." class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-admin focus:outline-none transition bg-gray-50 focus:bg-white">
            </div>

            <!-- Filters Row -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Date Range -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="flex items-center gap-1.5 px-3 py-2 border-2 border-gray-200 rounded-xl focus-within:border-admin transition bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-transparent text-sm focus:outline-none text-gray-700 w-36">
                    </div>
                    <span class="text-gray-400 font-medium">—</span>
                    <div class="flex items-center gap-1.5 px-3 py-2 border-2 border-gray-200 rounded-xl focus-within:border-admin transition bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-transparent text-sm focus:outline-none text-gray-700 w-36">
                    </div>
                </div>

                <select name="status" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-admin focus:outline-none bg-gray-50 transition">
                    <option value="">Semua Status</option>
                    <option value="publish" {{ ($status_filter ?? '') === 'publish' ? 'selected' : '' }}>Publish</option>
                    <option value="draft" {{ ($status_filter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ ($status_filter ?? '') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>

                <select name="gender" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-admin focus:outline-none bg-gray-50 transition">
                    <option value="">Semua Gender</option>
                    <option value="men" {{ ($gender_filter ?? '') === 'men' ? 'selected' : '' }}>Men</option>
                    <option value="women" {{ ($gender_filter ?? '') === 'women' ? 'selected' : '' }}>Women</option>
                    <option value="unisex" {{ ($gender_filter ?? '') === 'unisex' ? 'selected' : '' }}>Unisex</option>
                    <option value="kids" {{ ($gender_filter ?? '') === 'kids' ? 'selected' : '' }}>Kids</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-admin text-white rounded-xl text-sm font-bold hover:bg-admin-dark transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.master-product.index') }}" class="px-4 py-2.5 border-2 border-gray-200 text-gray-500 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Gambar</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Produk</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Supplier</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Gender</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk_list as $produk)
                        @php
                            $minStok = $produk->detailProduk->min('stok') ?? 0;
                            $totalStok = $produk->detailProduk->sum('stok') ?? 0;
                            $varianCount = $produk->detailProduk->count();
                            $statusBadge = $minStok === 0
                                ? ['bg-red-100 text-red-600', 'Out of Stock']
                                : ($minStok <= 5
                                    ? ['bg-yellow-100 text-yellow-700', 'Low Stock']
                                    : ['bg-green-100 text-green-700', 'Available']);
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">{{ $produk->formatted_id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($produk->gambarUtama)
                                    <img src="{{ $produk->gambarUtama->url_safe ?? asset('images/placeholder.png') }}" class="w-12 h-12 rounded-xl object-cover bg-gray-50" alt="{{ $produk->nama_produk }}">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800 text-sm">{{ $produk->nama_produk }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $produk->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $produk->supplier->nama_toko ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($produk->gender ?? '-') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 text-sm">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <div>
                                    <span class="inline-flex text-xs font-bold px-2.5 py-1 rounded-full {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $varianCount }} varian · {{ $totalStok }} stok</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.master-product.detail', $produk->produk_id) }}"
                                       class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-admin/10 hover:text-admin text-gray-500 transition"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.variant.index', ['produk_id' => $produk->produk_id]) }}"
                                       class="w-8 h-8 bg-admin/10 rounded-lg flex items-center justify-center hover:bg-admin hover:text-white text-admin transition"
                                       title="Kelola Varian ({{ $varianCount }} varian)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.media.index', ['produk_id' => $produk->produk_id]) }}"
                                       class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center hover:bg-purple-100 text-purple-600 transition"
                                       title="Kelola Media">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-500 mb-1">Belum ada produk</p>
                                        <p class="text-sm text-gray-400">Mulai tambahkan produk pertama Anda</p>
                                    </div>
                                    <a href="{{ route('admin.master-product.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-admin text-white rounded-xl text-sm font-bold hover:bg-admin-dark transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Produk Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($produk_list) && $produk_list->hasPages())
            <div class="mt-4 flex items-center justify-between px-4 pb-4">
                <p class="text-sm text-gray-400">Menampilkan {{ $produk_list->firstItem() }}–{{ $produk_list->lastItem() }} dari {{ $produk_list->total() }} produk</p>
                {{ $produk_list->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

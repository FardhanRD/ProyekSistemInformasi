@extends('layouts.admin')

@section('title', 'Supplier Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Supplier Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola supplier dan produk yang terhubung.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="#" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export</a>
            <a href="{{ route('admin.supplier.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">+ Add Supplier</a>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search for stores or supplier names..." class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <select name="sort" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="recent" {{ ($sort ?? '') === 'recent' ? 'selected' : '' }}>Recently Added</option>
                <option value="name_az" {{ ($sort ?? '') === 'name_az' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_za" {{ ($sort ?? '') === 'name_za' ? 'selected' : '' }}>Name Z-A</option>
            </select>

            <button type="submit" class="rounded-xl bg-teal-600 text-white px-5 py-2 text-sm font-semibold hover:bg-teal-700">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse(($suppliers ?? collect()) as $s)
            @php
                $isActive = ($s->is_verified ?? 0) == 1;
                $badgeClass = $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700';
                $avatar = strtoupper(substr($s->nama_owner ?? $s->nama_toko ?? '-',0,1));
            @endphp

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $badgeClass }}">ACTIVE</span>
                    <button type="button" class="text-slate-500">⋮</button>
                </div>

                <a href="{{ route('admin.supplier.detail', $s->supplier_id) }}" class="block mt-4">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold">
                            {{ $avatar }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-base">{{ $s->nama_toko }}</div>
                            <div class="text-xs text-slate-500">{{ $s->kategori_supplier ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-3 text-sm text-slate-700">
                        <div class="text-slate-600">{{ $s->alamat_toko ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-1">SKU: -</div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <a href="#" class="inline-flex items-center justify-center rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold">Contact</a>
                        <div class="text-xs text-slate-500">Last updated -</div>
                    </div>
                </a>

                <div class="mt-4 flex gap-2">
                    <form method="POST" action="{{ route('admin.supplier.destroy', $s->supplier_id) }}" onsubmit="return confirm('Hapus supplier ini?')" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 text-red-600 px-4 py-2 text-sm font-semibold">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-sm text-slate-500">Belum ada supplier.</div>
        @endforelse
    </div>

    @if(method_exists($suppliers ?? null, 'links'))
        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection


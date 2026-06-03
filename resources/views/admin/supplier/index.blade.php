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
            <a href="{{ route('admin.supplier.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#63A2BB] px-4 py-2 text-sm font-semibold text-white hover:bg-[#4e8fa8]">+ Add Supplier</a>
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

            <button type="submit" class="rounded-xl bg-[#63A2BB] text-white px-5 py-2 text-sm font-semibold hover:bg-[#4e8fa8]">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse(($suppliers ?? collect()) as $s)
            @php
                $isActive = ($s->is_verified ?? 0) == 1;
                $badgeClass = $isActive ? 'bg-[#63A2BB] text-white' : 'bg-slate-100 text-slate-700';
                $avatar = strtoupper(substr($s->nama_owner ?? $s->nama_toko ?? '-',0,1));
            @endphp

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm overflow-hidden">
                <div class="relative">
                    <div class="h-24 w-full bg-slate-50 flex items-center justify-center overflow-hidden rounded-2xl border border-slate-200">
                        @if(!empty($s->foto_toko))
                            <img src="{{ asset('storage/' . $s->foto_toko) }}" alt="Foto Toko {{ $s->nama_toko }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <i class="fas fa-store text-2xl"></i>
                                <span class="text-xs font-semibold mt-1">No Logo</span>
                            </div>
                        @endif
                    </div>
                    <div class="absolute top-3 left-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-full {{ $badgeClass }}">{{ $isActive ? 'ACTIVE' : 'INACTIVE' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.supplier.detail', $s->supplier_id) }}" class="block">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-[#63A2BB] text-white flex items-center justify-center font-bold">
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
                    </a>
                </div>

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



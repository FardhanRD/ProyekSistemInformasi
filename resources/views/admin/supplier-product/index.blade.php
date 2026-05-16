@extends('layouts.admin')

@section('title', 'Supplier Product Link')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Supplier Product Link</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola hubungan supplier dengan produk dan harga modal.</p>
        </div>

        <button type="button" onclick="document.getElementById('addRelationModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-[#2B9BAF] px-4 py-2 text-sm font-semibold text-white hover:bg-[#237f88]">
            + Add Relation
        </button>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
            <select name="supplier_id" class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Suppliers</option>
                @foreach($supplier_list as $s)
                    <option value="{{ $s->supplier_id }}" {{ ($supplier_filter ?? '') == $s->supplier_id ? 'selected' : '' }}>
                        {{ $s->nama_toko }}
                    </option>
                @endforeach
            </select>

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

    {{-- Relations Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">Harga Modal</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relations as $rel)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $rel->supplier->nama_toko ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $rel->supplier->nama_owner ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $rel->produk->nama_produk ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">
                                    Rp {{ number_format($rel->harga_modal ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-slate-600 text-xs">{{ $rel->catatan ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <button type="button" class="text-slate-600 hover:text-slate-900" onclick="editRelation({{ $rel->produk_supplier_id }}, {{ $rel->harga_modal }}, '{{ $rel->catatan }}')">✎</button>
                                    <form method="POST" action="{{ route('admin.supplier-product.destroy', $rel->produk_supplier_id) }}" onsubmit="return confirm('Hapus relasi ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-600 hover:text-red-600">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-4 text-center text-slate-600">
                                Belum ada relasi supplier-produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($relations ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $relations->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Add Relation Modal --}}
<div id="addRelationModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Tambah Relasi Supplier-Produk</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('addRelationModal').classList.add('hidden')">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.supplier-product.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm font-semibold text-slate-700">Supplier</label>
                <select name="supplier_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
                    <option value="">Pilih supplier...</option>
                    @foreach($supplier_list as $s)
                        <option value="{{ $s->supplier_id }}">{{ $s->nama_toko }}</option>
                    @endforeach
                </select>
            </div>

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
                <label class="text-sm font-semibold text-slate-700">Harga Modal</label>
                <input type="number" name="harga_modal" required step="0.01" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Catatan (optional)</label>
                <textarea name="catatan" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('addRelationModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Relation Modal --}}
<div id="editRelationModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Edit Relasi</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('editRelationModal').classList.add('hidden')">✕</button>
        </div>

        <form id="editRelationForm" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="text-sm font-semibold text-slate-700">Harga Modal</label>
                <input type="number" id="editHargaModal" name="harga_modal" required step="0.01" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Catatan</label>
                <textarea id="editCatatan" name="catatan" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('editRelationModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editRelation(relationId, hargaModal, catatan) {
    document.getElementById('editHargaModal').value = hargaModal;
    document.getElementById('editCatatan').value = catatan;
    document.getElementById('editRelationForm').action = `/admin/supplier-product/${relationId}`;
    document.getElementById('editRelationModal').classList.remove('hidden');
}
</script>
@endsection

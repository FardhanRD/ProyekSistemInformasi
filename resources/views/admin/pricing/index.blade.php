@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Pricing Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola harga untuk setiap variant produk.</p>
        </div>
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

    {{-- Variants Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Variant (Warna / Size)</th>
                        <th class="py-3 px-4">Harga Saat Ini</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $v)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $v->produk->nama_produk ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">{{ $v->sku ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="text-slate-600">
                                    {{ $v->warna->nama_warna ?? '-' }} 
                                    {{ $v->ukuran ? '/ ' . $v->ukuran : '' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">
                                    Rp {{ number_format($v->harga ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <button type="button" class="text-slate-600 hover:text-slate-900 font-medium" onclick="editPrice({{ $v->detail_produk_id }}, {{ $v->harga ?? 0 }})">
                                    ✎ Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-4 text-center text-slate-600">
                                Belum ada variant.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($variants ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $variants->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Edit Price Modal --}}
<div id="editPriceModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Edit Harga</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('editPriceModal').classList.add('hidden')">✕</button>
        </div>

        <form id="editPriceForm" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="text-sm font-semibold text-slate-700">Harga Baru</label>
                <input type="number" id="priceInput" name="harga" required step="0.01" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('editPriceModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function editPrice(variantId, currentPrice) {
    document.getElementById('priceInput').value = currentPrice;
    document.getElementById('editPriceForm').action = `/admin/pricing/${variantId}`;
    document.getElementById('editPriceModal').classList.remove('hidden');
}
</script>
@endsection

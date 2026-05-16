@extends('layouts.admin')

@section('title', 'Stock Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Stock Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola stok variant produk dan minimum stock alert.</p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm">
            <span class="font-semibold text-red-700">⚠ Low Stock: {{ $low_stock_count }}</span>
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

            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="ok" {{ ($status_filter ?? '') === 'ok' ? 'selected' : '' }}>OK (Stok Cukup)</option>
                <option value="low" {{ ($status_filter ?? '') === 'low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out" {{ ($status_filter ?? '') === 'out' ? 'selected' : '' }}>Out of Stock</option>
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
                        <th class="py-3 px-4">Stok Saat Ini</th>
                        <th class="py-3 px-4">Min Stok</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $v)
                        @php
                            $stok_status = 'ok';
                            if ($v->stok <= $v->stok_minimum) {
                                $stok_status = $v->stok == 0 ? 'out' : 'low';
                            }
                        @endphp
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
                                <div class="font-semibold text-slate-900">{{ $v->stok ?? 0 }} pcs</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-slate-600">{{ $v->stok_minimum ?? 0 }} pcs</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($stok_status === 'ok')
                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">OK</span>
                                @elseif($stok_status === 'low')
                                    <span class="bg-yellow-50 text-yellow-700 text-xs px-2 py-1 rounded-full font-semibold">LOW</span>
                                @else
                                    <span class="bg-red-50 text-red-700 text-xs px-2 py-1 rounded-full font-semibold">OUT</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <button type="button" class="text-slate-600 hover:text-slate-900 font-medium" onclick="adjustStock({{ $v->detail_produk_id }}, {{ $v->stok }})">
                                    🔧 Adjust
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 px-4 text-center text-slate-600">
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

{{-- Adjust Stock Modal --}}
<div id="adjustStockModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Adjust Stock</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('adjustStockModal').classList.add('hidden')">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.stock.adjust') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="detail_produk_id" id="variantId">

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Stok Saat Ini</div>
                <div class="text-2xl font-bold text-slate-900" id="currentStock">0</div>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Penyesuaian Stok (+ atau -)</label>
                <input type="number" name="qty" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2" placeholder="Contoh: +5 atau -3">
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Catatan</label>
                <textarea name="catatan" rows="3" placeholder="Alasan penyesuaian..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('adjustStockModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function adjustStock(variantId, currentStock) {
    document.getElementById('variantId').value = variantId;
    document.getElementById('currentStock').textContent = currentStock + ' pcs';
    document.getElementById('adjustStockModal').classList.remove('hidden');
}
</script>
@endsection

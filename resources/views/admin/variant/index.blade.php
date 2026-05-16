@extends('layouts.admin')

@section('title', 'Variant Management')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Variant Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola warna, ukuran, dan stok produk.</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">TOTAL VARIANTS</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $total_variants }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700">+2%</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">UNIQUE COLORS</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $unique_colors->count() }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">Info</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">LOW STOCK ALERT</div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $low_stock_alert }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700">Warning</div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">LAST SYNC</div>
                    <div class="text-sm font-bold text-slate-900 mt-2">{{ $last_sync->format('H:i') }} {{ $last_sync->format('D, d M') }}</div>
                </div>
                <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Live</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-end">
            <input type="text" name="q" placeholder="Search by product name or SKU..." value="{{ $search ?? '' }}" class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <select name="color" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Colors</option>
                @foreach($unique_colors as $c)
                    <option value="{{ $c->warna_id }}" {{ ($color ?? '') == $c->warna_id ? 'selected' : '' }}>{{ $c->nama_warna }}</option>
                @endforeach
            </select>

            <select name="size" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Sizes</option>
                @foreach($unique_sizes as $s)
                    <option value="{{ $s['id'] }}" {{ ($size ?? '') == $s['id'] ? 'selected' : '' }}>{{ $s['nama'] }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="available" {{ ($status ?? '') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="low" {{ ($status ?? '') === 'low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ ($status ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-5 py-2 text-sm font-semibold hover:bg-[#237f88]">Apply</button>
        </form>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-3 mb-6">
        <button type="button" onclick="document.getElementById('addColorModal').classList.remove('hidden')" class="rounded-xl bg-slate-100 text-slate-900 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
            + Add Color
        </button>
        <button type="button" onclick="document.getElementById('addSizeModal').classList.remove('hidden')" class="rounded-xl bg-slate-100 text-slate-900 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
            + Add Size
        </button>
    </div>

    {{-- Accordion Variant Table --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        @forelse($variants->groupBy('produk_id') ?? collect() as $produk_id => $variantGroup)
            @php
                $produk = $variantGroup->first()->produk;
            @endphp
            <div class="border-b border-slate-100">
                <button type="button" class="w-full px-5 py-4 flex items-center justify-between hover:bg-slate-50 transition accordion-toggle" onclick="toggleAccordion(this)">
                    <div class="flex items-center gap-4 flex-1 text-left">
                        <div class="text-slate-900 font-semibold">{{ $produk->nama_produk ?? 'Unknown Product' }}</div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">{{ count($variantGroup) }} variants</span>
                    </div>
                    <span class="text-slate-400 transition accordion-icon">▼</span>
                </button>

                <div class="accordion-content hidden">
                    <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr class="text-slate-500 font-semibold">
                                        <th class="text-left py-2 px-2">SKU</th>
                                        <th class="text-left py-2 px-2">Color</th>
                                        <th class="text-left py-2 px-2">Size</th>
                                        <th class="text-left py-2 px-2">Total Stock</th>
                                        <th class="text-left py-2 px-2">Price Range</th>
                                        <th class="text-left py-2 px-2">Status</th>
                                        <th class="text-left py-2 px-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($variantGroup as $variant)
                                        <tr class="border-t border-slate-100">
                                            <td class="py-2 px-2">{{ $variant->sku ?? '-' }}</td>
                                            <td class="py-2 px-2">
                                                @if($variant->warna)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-4 h-4 rounded-full border border-slate-300" style="background-color: {{ $variant->warna->kode_hex ?? '#000' }};"></div>
                                                        <span>{{ $variant->warna->nama_warna }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-2">{{ $variant->ukuran ?? '-' }}</td>
                                            <td class="py-2 px-2">{{ $variant->stok_total ?? 0 }} pcs</td>
                                            <td class="py-2 px-2">Rp {{ number_format($variant->harga_pokok ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-2 px-2">
                                                @if(($variant->status_stok ?? 'available') === 'available')
                                                    <span class="bg-green-50 text-green-700 text-xs px-2 py-1 rounded font-semibold">Available</span>
                                                @elseif(($variant->status_stok ?? 'available') === 'low')
                                                    <span class="bg-yellow-50 text-yellow-700 text-xs px-2 py-1 rounded font-semibold">Low</span>
                                                @else
                                                    <span class="bg-red-50 text-red-700 text-xs px-2 py-1 rounded font-semibold">Out</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-2">
                                                <button type="button" class="text-slate-600 hover:text-slate-900" onclick="editVariant({{ $variant->detail_produk_id }})">✎</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-5 py-4 text-slate-600">
                Belum ada variant terdaftar.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($variants ?? null, 'links'))
        <div class="mt-4">{{ $variants->links() }}</div>
    @endif
</div>

{{-- Add Color Modal --}}
<div id="addColorModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Add New Color</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('addColorModal').classList.add('hidden')">✕</button>
        </div>

        <form id="addColorForm" method="POST" action="{{ route('admin.variant.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="color">

            <div>
                <label class="text-sm font-semibold text-slate-700">Select Product</label>
                <select name="produk_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
                    <option value="">Choose product...</option>
                    @foreach(($variants->unique('produk_id') ?? collect()) as $v)
                        @if($v->produk)
                            <option value="{{ $v->produk->produk_id }}">{{ $v->produk->nama_produk }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Color Name</label>
                <input type="text" name="value" required placeholder="e.g., Red, Blue, Green" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('addColorModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Add Color</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Size Modal --}}
<div id="addSizeModal" class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-3xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-xl text-slate-900">Add New Size</h3>
            <button type="button" class="text-slate-500" onclick="document.getElementById('addSizeModal').classList.add('hidden')">✕</button>
        </div>

        <form id="addSizeForm" method="POST" action="{{ route('admin.variant.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="size">

            <div>
                <label class="text-sm font-semibold text-slate-700">Select Product</label>
                <select name="produk_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
                    <option value="">Choose product...</option>
                    @foreach(($variants->unique('produk_id') ?? collect()) as $v)
                        @if($v->produk)
                            <option value="{{ $v->produk->produk_id }}">{{ $v->produk->nama_produk }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Size</label>
                <input type="text" name="value" required placeholder="e.g., S, M, L, XL, or 36, 37, 38" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm mt-2">
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700" onclick="document.getElementById('addSizeModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-4 py-2 text-sm font-semibold hover:bg-[#237f88]">Add Size</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('.accordion-icon');
    
    content.classList.toggle('hidden');
    if (content.classList.contains('hidden')) {
        icon.textContent = '▼';
    } else {
        icon.textContent = '▲';
    }
}

function editVariant(variantId) {
    // Redirect to edit variant page (can implement if needed)
    alert('Edit variant ' + variantId);
}
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Create Supplier Order')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Buat Purchase Order Baru</h1>
        <p class="text-slate-600 text-sm mt-1">Masukkan data supplier, pilih produk, dan tentukan qty & harga beli.</p>
    </div>

    <form method="POST" action="{{ route('admin.supplier-order.store') }}" x-data="orderForm()" class="space-y-6">
        @csrf

        {{-- Supplier Selection --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <label class="block text-sm font-semibold text-slate-900 mb-3">Supplier</label>
            <select name="supplier_id" required @change="selectedSupplier = $el.value" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#2B9BAF]">
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supp)
                    <option value="{{ $supp->supplier_id }}">{{ $supp->nama_toko }} ({{ $supp->pemilik }})</option>
                @endforeach
            </select>
            @error('supplier_id')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Items Section --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-slate-900">Item Pesanan</h2>
                <button type="button" @click="addItem()" class="rounded-xl bg-green-500 text-white px-3 py-2 text-sm font-semibold hover:bg-green-600">+ Tambah Item</button>
            </div>

            <div class="space-y-3" x-show="items.length > 0">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                        <div class="grid sm:grid-cols-4 gap-3">
                            {{-- Produk Select --}}
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Produk</label>
                                <select x-model="item.detail_produk_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                    <option value="">Pilih...</option>
                                    @foreach(($detailProducts ?? []) as $detail)
                                        <option value="{{ $detail->detail_produk_id }}">
                                            {{ $detail->produk?->nama_produk ?? '-' }}
                                            @if($detail->warna?->nama_warna)
                                                - {{ $detail->warna->nama_warna }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Qty --}}
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Qty</label>
                                <input type="number" x-model.number="item.qty" min="1" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            </div>

                            {{-- Harga Beli --}}
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Harga Beli</label>
                                <input type="number" x-model.number="item.harga_beli" min="0" step="100" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            </div>

                            {{-- Remove --}}
                            <div class="flex items-end">
                                <button type="button" @click="removeItem(idx)" class="w-full rounded-lg bg-red-500 text-white py-2 text-sm font-semibold hover:bg-red-600">Hapus</button>
                            </div>
                        </div>

                        {{-- Hidden inputs --}}
                        <input type="hidden" :name="`items[${idx}][detail_produk_id]`" :value="item.detail_produk_id">
                        <input type="hidden" :name="`items[${idx}][qty]`" :value="item.qty">
                        <input type="hidden" :name="`items[${idx}][harga_beli]`" :value="item.harga_beli">
                    </div>
                </template>
            </div>

            @error('items')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror

            <div x-show="items.length === 0" class="text-center py-8 text-slate-600">
                Belum ada item. Klik "Tambah Item" untuk mulai.
            </div>
        </div>

        {{-- Catatan --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <label class="block text-sm font-semibold text-slate-900 mb-3">Catatan (Opsional)</label>
            <textarea name="catatan" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-[#2B9BAF]" placeholder="Tambahkan catatan khusus untuk supplier..."></textarea>
        </div>

        {{-- Summary --}}
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-slate-600 text-xs font-semibold mb-1">Total Item</p>
                <p class="text-2xl font-bold text-slate-900" x-text="totalItems()">0</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-slate-600 text-xs font-semibold mb-1">Total Harga</p>
                <p class="text-2xl font-bold text-[#2B9BAF]" x-text="'Rp ' + formatCurrency(totalPrice())">Rp 0</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-slate-600 text-xs font-semibold mb-1">Rata-Rata Unit</p>
                <p class="text-2xl font-bold text-slate-900" x-text="avgUnitPrice()">Rp 0</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.supplier-order.index') }}" class="rounded-xl border border-slate-200 text-slate-900 px-6 py-3 font-semibold hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-xl bg-[#2B9BAF] text-white px-6 py-3 font-semibold hover:bg-[#237f88]">Buat PO</button>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: [],
        selectedSupplier: null,

        addItem() {
            this.items.push({
                detail_produk_id: '',
                qty: 1,
                harga_beli: 0,
            });
        },

        removeItem(idx) {
            this.items.splice(idx, 1);
        },

        totalItems() {
            return this.items.reduce((sum, item) => sum + (item.qty || 0), 0);
        },

        totalPrice() {
            return this.items.reduce((sum, item) => sum + ((item.qty || 0) * (item.harga_beli || 0)), 0);
        },

        avgUnitPrice() {
            const total = this.totalPrice();
            const count = this.items.length;
            return count > 0 ? total / count : 0;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(Math.floor(value || 0));
        }
    }
}
</script>
@endsection

@extends('layouts.buyer')

@section('title', 'MOVR | Keranjang')

@section('content')
@php
    $cartData = collect($items ?? [])->map(function ($item) {
        $produk = $item->detail->produk ?? null;
        $gambar = optional(optional($produk)->images->first())->url_lengkap
            ?? (optional(optional($produk)->images->first())->url_gambar ? asset('storage/' . $produk->images->first()->url_gambar) : asset('images/default-product.svg'));

        return [
            'keranjang_id' => $item->keranjang_id,
            'detail_produk_id' => $item->detail_produk_id,
            'slug' => optional($produk)->slug,
            'nama_produk' => $item->detail->nama_produk ?? optional($produk)->nama_produk ?? 'Produk',
            'ukuran' => $item->detail->ukuran ?? '-',
            'warna_nama' => optional($item->detail->warna)->nama_warna,
            'harga' => (float) ($item->detail->harga ?? 0),
            'jumlah' => (int) ($item->jumlah ?? 1),
            'stok' => (int) ($item->detail->stok ?? 1),
            'image' => $gambar,
        ];
    })->values();
@endphp

<div class="space-y-6" x-data="cartPage(@json($cartData))">
    <div>
        <div class="text-xs font-semibold text-cyan-300">KERANJANG</div>
        <h1 class="text-2xl md:text-3xl font-black">Keranjang Belanja</h1>
    </div>

    <template x-if="items.length === 0">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
            <img src="{{ asset('images/cart-empty.svg') }}" alt="Keranjang kosong" class="mx-auto mb-6 h-48 w-48 object-contain">
            <h2 class="text-2xl font-bold">Keranjang kamu masih kosong</h2>
            <p class="mt-2 text-sm text-slate-300">Mulai pilih produk favoritmu sekarang.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex rounded-full bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">
                Mulai Belanja
            </a>
        </div>
    </template>

    <template x-if="items.length > 0">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8 rounded-3xl border border-white/10 bg-white/5 p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-200">
                        <input type="checkbox" class="h-4 w-4 rounded border-slate-600 text-cyan-500" x-model="selectAll">
                        <span>Pilih Semua</span>
                    </label>
                    <button type="button" @click="removeSelected()" class="text-sm font-semibold text-rose-300 hover:text-rose-200">
                        Hapus yang dipilih
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <template x-for="item in items" :key="item.keranjang_id">
                        <div class="rounded-3xl border border-white/10 bg-black/20 p-4 transition" :class="{'opacity-60': item.removing}">
                            <div class="flex items-start gap-4">
                                <input type="checkbox" class="mt-2 h-4 w-4 rounded border-slate-600 text-cyan-500" x-model="item.checked" @change="syncSelection()">
                                <img :src="item.image" :alt="item.nama_produk" class="h-24 w-24 rounded-2xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                        <div class="min-w-0">
                                            <a :href="'/product/' + item.slug" class="block truncate text-base font-bold text-white hover:text-cyan-300" x-text="item.nama_produk"></a>
                                            <div class="mt-2 space-y-1 text-sm text-slate-300">
                                                <div>Size: <span class="font-semibold" x-text="item.ukuran"></span></div>
                                                <div>Warna: <span class="font-semibold" x-text="item.warna_nama || '-' "></span></div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-black text-white" x-text="fmt(item.harga)"></div>
                                            <button type="button" @click="remove(item)" class="mt-2 inline-flex items-center gap-1 rounded-full border border-white/10 px-3 py-1 text-xs font-semibold text-rose-300 hover:bg-rose-500/10">
                                                🗑 Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div class="inline-flex items-center overflow-hidden rounded-2xl border border-white/10">
                                            <button type="button" @click="decrease(item)" class="px-4 py-2 text-lg font-bold hover:bg-white/5">−</button>
                                            <input type="number" min="1" :max="item.stok" x-model.number="item.jumlah" @change="persistQty(item)" class="w-16 border-x border-white/10 bg-transparent py-2 text-center text-sm outline-none">
                                            <button type="button" @click="increase(item)" class="px-4 py-2 text-lg font-bold hover:bg-white/5">+</button>
                                        </div>
                                        <div class="text-sm text-slate-300">
                                            Subtotal: <span class="font-bold text-white" x-text="fmt(item.harga * item.jumlah)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 lg:sticky lg:top-24">
                    <h2 class="text-lg font-bold">Ringkasan Belanja</h2>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">Subtotal</span>
                            <span class="font-bold" x-text="fmt(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-300">Item dipilih</span>
                            <span class="font-bold" x-text="selectedCount"></span>
                        </div>
                    </div>

                    <button type="button" @click="checkout()" class="mt-6 w-full rounded-3xl bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">
                        Checkout →
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@section('scripts')
<script>
function cartPage(initialItems) {
    return {
        items: initialItems.map((item) => ({
            ...item,
            checked: true,
            removing: false,
            slug: item.slug || '',
        })),
        get selectAll() {
            return this.items.length > 0 && this.items.every((item) => item.checked);
        },
        set selectAll(value) {
            this.items.forEach((item) => item.checked = value);
        },
        get selectedItems() {
            return this.items.filter((item) => item.checked);
        },
        get subtotal() {
            return this.selectedItems.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        },
        get selectedCount() {
            return this.selectedItems.length;
        },
        fmt(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        },
        syncSelection() {},
        decrease(item) {
            if (item.jumlah <= 1) return;
            item.jumlah -= 1;
            this.persistQty(item);
        },
        increase(item) {
            if (item.jumlah >= item.stok) return;
            item.jumlah += 1;
            this.persistQty(item);
        },
        async persistQty(item) {
            const jumlah = Math.max(1, Math.min(item.jumlah, item.stok || item.jumlah));
            item.jumlah = jumlah;

            try {
                const response = await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        keranjang_id: item.keranjang_id,
                        jumlah: jumlah,
                    }),
                });

                const data = await response.json();
                if (!data.success) {
                    alert(data.message || 'Gagal memperbarui jumlah');
                } else {
                    window.dispatchEvent(new Event('cart-updated'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },
        async remove(item) {
            if (!confirm('Hapus item ini dari keranjang?')) return;
            item.removing = true;

            try {
                const response = await fetch('/cart/remove/' + item.keranjang_id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (data.success) {
                    this.items = this.items.filter((current) => current.keranjang_id !== item.keranjang_id);
                    window.dispatchEvent(new Event('cart-updated'));
                } else {
                    item.removing = false;
                    alert(data.message || 'Gagal menghapus item');
                }
            } catch (error) {
                item.removing = false;
                alert('Error: ' + error.message);
            }
        },
        async removeSelected() {
            if (this.selectedItems.length === 0) {
                alert('Tidak ada item dipilih');
                return;
            }

            if (!confirm('Hapus item yang dipilih?')) return;
            for (const item of [...this.selectedItems]) {
                await this.remove(item);
            }
        },
        checkout() {
            const selected = this.selectedItems.map((item) => item.keranjang_id);
            if (selected.length === 0) {
                alert('Pilih minimal 1 item untuk checkout');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('checkout.store') }}';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(token);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'keranjang_ids';
            input.value = JSON.stringify(selected);
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        },
    };
}
</script>
@endsection

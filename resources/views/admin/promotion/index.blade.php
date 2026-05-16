@extends('layouts.admin')

@section('title', 'Promotion Management')

@section('content')
<div x-data="promotionPage()" class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Promotion Management</h1>
                <p class="text-slate-600">Kelola voucher, diskon produk, dan flash sale.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button @click="tab='voucher'" :class="tab==='voucher' ? activeTab : inactiveTab" class="px-4 py-2 rounded-xl text-sm font-semibold">Voucher</button>
                <button @click="tab='diskon'" :class="tab==='diskon' ? activeTab : inactiveTab" class="px-4 py-2 rounded-xl text-sm font-semibold">Diskon Produk</button>
                <button @click="tab='flash'" :class="tab==='flash' ? activeTab : inactiveTab" class="px-4 py-2 rounded-xl text-sm font-semibold">Flash Sale</button>
            </div>
        </div>
    </div>

    <template x-if="tab==='voucher'">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Voucher</h2>
                <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold" @click="openVoucherModal('create')">+ Tambah Voucher</button>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase text-slate-600">
                                <th class="px-4 py-3">Kode</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Jenis Diskon</th><th class="px-4 py-3">Nilai</th><th class="px-4 py-3">Min Belanja</th><th class="px-4 py-3">Kuota/Terpakai</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3 font-mono">{{ $voucher->kode_voucher }}</td>
                                    <td class="px-4 py-3">{{ $voucher->nama_voucher }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($voucher->jenis_diskon) }}</td>
                                    <td class="px-4 py-3">{{ $voucher->jenis_diskon === 'persen' ? $voucher->nilai_diskon.'%' : 'Rp '.number_format($voucher->nilai_diskon,0,',','.') }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($voucher->min_belanja ?? 0,0,',','.') }}</td>
                                    <td class="px-4 py-3">{{ $voucher->kuota ?? 'Unlimited' }} / {{ $voucher->kuota_terpakai ?? 0 }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $voucher->berlaku_mulai }}<br>{{ $voucher->berlaku_sampai }}</td>
                                    <td class="px-4 py-3">@if($voucher->is_active)<span class="px-2 py-1 rounded-full bg-green-100 text-green-700">Aktif</span>@else<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>@endif</td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <button class="text-blue-600" @click='openVoucherModal("edit", @json($voucher))'>Edit</button>
                                        <form method="POST" action="{{ route('admin.promotion.voucher.destroy', $voucher->voucher_id) }}" onsubmit="return confirm('Hapus voucher ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-4 py-6 text-center text-slate-500">Belum ada voucher.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    <template x-if="tab==='diskon'">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Diskon Produk</h2>
                <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold" @click="openPromoModal('create', 'diskon_produk')">+ Tambah Promo</button>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Nama Promo</th><th class="px-4 py-3">Produk/Variant</th><th class="px-4 py-3">Diskon</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($diskonProduk as $promo)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3">{{ $promo->nama_promo }}</td>
                                    <td class="px-4 py-3">{{ $promo->detailProduk?->produk?->nama_produk ?? $promo->produk?->nama_produk ?? '-' }} @if($promo->detailProduk) / {{ $promo->detailProduk?->warna?->nama_warna ?? '' }} @endif</td>
                                    <td class="px-4 py-3">{{ $promo->persen_diskon }}%</td>
                                    <td class="px-4 py-3 text-xs">{{ $promo->mulai }}<br>{{ $promo->selesai }}</td>
                                    <td class="px-4 py-3">@if($promo->is_active)<span class="px-2 py-1 rounded-full bg-green-100 text-green-700">Aktif</span>@else<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>@endif</td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <button class="text-blue-600" @click='openPromoModal("edit", @json($promo), "diskon_produk")'>Edit</button>
                                        <form method="POST" action="{{ route('admin.promotion.promo.destroy', $promo->promo_id) }}" onsubmit="return confirm('Hapus promo ini?')">@csrf @method('DELETE')<button class="text-red-600" type="submit">Hapus</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada diskon produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    <template x-if="tab==='flash'">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Flash Sale</h2>
                <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold" @click="openPromoModal('create', 'flash_sale')">+ Tambah Flash Sale</button>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Nama Promo</th><th class="px-4 py-3">Produk/Variant</th><th class="px-4 py-3">Diskon</th><th class="px-4 py-3">Stok Flash</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($flashSale as $promo)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3">{{ $promo->nama_promo }}</td>
                                    <td class="px-4 py-3">{{ $promo->detailProduk?->produk?->nama_produk ?? $promo->produk?->nama_produk ?? '-' }} @if($promo->detailProduk) / {{ $promo->detailProduk?->warna?->nama_warna ?? '' }} @endif</td>
                                    <td class="px-4 py-3">{{ $promo->persen_diskon }}%</td>
                                    <td class="px-4 py-3">{{ $promo->stok_flash_sale ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $promo->mulai }}<br>{{ $promo->selesai }}</td>
                                    <td class="px-4 py-3">@if($promo->is_active)<span class="px-2 py-1 rounded-full bg-green-100 text-green-700">Aktif</span>@else<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>@endif</td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <button class="text-blue-600" @click='openPromoModal("edit", @json($promo), "flash_sale")'>Edit</button>
                                        <form method="POST" action="{{ route('admin.promotion.promo.destroy', $promo->promo_id) }}" onsubmit="return confirm('Hapus flash sale ini?')">@csrf @method('DELETE')<button class="text-red-600" type="submit">Hapus</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada flash sale.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    {{-- Voucher Modal --}}
    <div x-show="voucherModal" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" style="display:none;">
        <div class="bg-white rounded-3xl p-6 w-full max-w-2xl shadow-xl">
            <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold" x-text="voucherMode==='create' ? 'Tambah Voucher' : 'Edit Voucher'"></h3><button class="text-slate-500" @click="voucherModal=false">✕</button></div>
            <form :action="voucherAction" method="POST" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" :value="voucherMethod">
                <div><label class="text-sm font-semibold">Kode</label><input name="kode_voucher" x-model="voucherForm.kode_voucher" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Nama Voucher</label><input name="nama_voucher" x-model="voucherForm.nama_voucher" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Jenis Diskon</label><select name="jenis_diskon" x-model="voucherForm.jenis_diskon" class="w-full rounded-xl border px-4 py-2"><option value="persen">Persen</option><option value="nominal">Nominal</option><option value="ongkir">Ongkir</option></select></div>
                <div><label class="text-sm font-semibold">Nilai Diskon</label><input type="number" step="0.01" name="nilai_diskon" x-model="voucherForm.nilai_diskon" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Min Belanja</label><input type="number" step="0.01" name="min_belanja" x-model="voucherForm.min_belanja" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Maks Diskon</label><input type="number" step="0.01" name="maks_diskon" x-model="voucherForm.maks_diskon" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Kuota</label><input type="number" name="kuota" x-model="voucherForm.kuota" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Berlaku Mulai</label><input type="datetime-local" name="berlaku_mulai" x-model="voucherForm.berlaku_mulai" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Berlaku Sampai</label><input type="datetime-local" name="berlaku_sampai" x-model="voucherForm.berlaku_sampai" class="w-full rounded-xl border px-4 py-2"></div>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" x-model="voucherForm.is_active"> Aktif</label>
                <div class="sm:col-span-2 flex justify-end gap-2 pt-2"><button type="button" @click="voucherModal=false" class="px-4 py-2 border rounded-xl">Batal</button><button class="px-4 py-2 rounded-xl bg-[#2B9BAF] text-white">Simpan</button></div>
            </form>
        </div>
    </div>

    {{-- Promo Modal --}}
    <div x-show="promoModal" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" style="display:none;">
        <div class="bg-white rounded-3xl p-6 w-full max-w-2xl shadow-xl max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold" x-text="promoMode==='create' ? 'Tambah Promo' : 'Edit Promo'"></h3><button class="text-slate-500" @click="promoModal=false">✕</button></div>
            <form :action="promoAction" method="POST" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" :value="promoMethod">
                <div><label class="text-sm font-semibold">Jenis</label><select name="jenis" x-model="promoForm.jenis" class="w-full rounded-xl border px-4 py-2"><option value="diskon_produk">Diskon Produk</option><option value="flash_sale">Flash Sale</option></select></div>
                <div><label class="text-sm font-semibold">Nama Promo</label><input name="nama_promo" x-model="promoForm.nama_promo" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Produk</label><select name="produk_id" x-model="promoForm.produk_id" class="w-full rounded-xl border px-4 py-2"><option value="">Global / pilih produk</option>@foreach($products as $product)<option value="{{ $product->produk_id }}">{{ $product->nama_produk }}</option>@endforeach</select></div>
                <div><label class="text-sm font-semibold">Variant</label><select name="detail_produk_id" x-model="promoForm.detail_produk_id" class="w-full rounded-xl border px-4 py-2"><option value="">Pilih variant</option>@foreach($variants as $variant)<option value="{{ $variant->detail_produk_id }}">{{ $variant->produk?->nama_produk }} - {{ $variant->warna?->nama_warna ?? $variant->nama_produk }}</option>@endforeach</select></div>
                <div><label class="text-sm font-semibold">Persen Diskon</label><input type="number" step="0.01" name="persen_diskon" x-model="promoForm.persen_diskon" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Nominal Diskon</label><input type="number" step="0.01" name="nominal_diskon" x-model="promoForm.nominal_diskon" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Stok Flash Sale</label><input type="number" name="stok_flash_sale" x-model="promoForm.stok_flash_sale" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Mulai</label><input type="datetime-local" name="mulai" x-model="promoForm.mulai" class="w-full rounded-xl border px-4 py-2"></div>
                <div><label class="text-sm font-semibold">Selesai</label><input type="datetime-local" name="selesai" x-model="promoForm.selesai" class="w-full rounded-xl border px-4 py-2"></div>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" x-model="promoForm.is_active"> Aktif</label>
                <div class="sm:col-span-2 flex justify-end gap-2 pt-2"><button type="button" @click="promoModal=false" class="px-4 py-2 border rounded-xl">Batal</button><button class="px-4 py-2 rounded-xl bg-[#2B9BAF] text-white">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function promotionPage() {
    return {
        tab: 'voucher',
        voucherModal: false,
        promoModal: false,
        voucherMode: 'create',
        promoMode: 'create',
        voucherAction: '{{ route('admin.promotion.voucher.store') }}',
        voucherMethod: 'POST',
        promoAction: '{{ route('admin.promotion.promo.store') }}',
        promoMethod: 'POST',
        voucherForm: { kode_voucher:'', nama_voucher:'', jenis_diskon:'persen', nilai_diskon:0, min_belanja:0, maks_diskon:'', kuota:'', berlaku_mulai:'', berlaku_sampai:'', is_active:true },
        promoForm: { jenis:'diskon_produk', nama_promo:'', produk_id:'', detail_produk_id:'', persen_diskon:0, nominal_diskon:'', stok_flash_sale:'', mulai:'', selesai:'', is_active:true },
        activeTab: 'bg-[#2B9BAF] text-white',
        inactiveTab: 'bg-slate-100 text-slate-700',
        openVoucherModal(mode, voucher = {}) {
            this.voucherMode = mode;
            this.voucherModal = true;
            this.voucherAction = mode === 'create' ? '{{ route('admin.promotion.voucher.store') }}' : '{{ route('admin.promotion.voucher.update', ['id' => '__ID__']) }}'.replace('__ID__', voucher.voucher_id);
            this.voucherMethod = mode === 'create' ? 'POST' : 'PUT';
            this.voucherForm = {
                kode_voucher: voucher.kode_voucher ?? '',
                nama_voucher: voucher.nama_voucher ?? '',
                jenis_diskon: voucher.jenis_diskon ?? 'persen',
                nilai_diskon: voucher.nilai_diskon ?? 0,
                min_belanja: voucher.min_belanja ?? 0,
                maks_diskon: voucher.maks_diskon ?? '',
                kuota: voucher.kuota ?? '',
                berlaku_mulai: voucher.berlaku_mulai ? String(voucher.berlaku_mulai).replace(' ', 'T').slice(0,16) : '',
                berlaku_sampai: voucher.berlaku_sampai ? String(voucher.berlaku_sampai).replace(' ', 'T').slice(0,16) : '',
                is_active: !!voucher.is_active,
            };
        },
        openPromoModal(mode, promoType, promo = {}) {
            this.promoMode = mode;
            this.promoModal = true;
            this.promoAction = mode === 'create' ? '{{ route('admin.promotion.promo.store') }}' : '{{ route('admin.promotion.promo.update', ['id' => '__ID__']) }}'.replace('__ID__', promo.promo_id);
            this.promoMethod = mode === 'create' ? 'POST' : 'PUT';
            this.promoForm = {
                jenis: promoType ?? promo.jenis ?? 'diskon_produk',
                nama_promo: promo.nama_promo ?? '',
                produk_id: promo.produk_id ?? '',
                detail_produk_id: promo.detail_produk_id ?? '',
                persen_diskon: promo.persen_diskon ?? 0,
                nominal_diskon: promo.nominal_diskon ?? '',
                stok_flash_sale: promo.stok_flash_sale ?? '',
                mulai: promo.mulai ? String(promo.mulai).replace(' ', 'T').slice(0,16) : '',
                selesai: promo.selesai ? String(promo.selesai).replace(' ', 'T').slice(0,16) : '',
                is_active: !!promo.is_active,
            };
        },
    }
}
</script>
@endsection

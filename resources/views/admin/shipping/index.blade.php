@extends('layouts.admin')

@section('title', 'Shipping Management')

@section('content')
<div x-data="shippingPage()" class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Shipping Management</h1>
                <p class="text-slate-600">Kelola ekspedisi dan update tracking aktif.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button @click="tab='ekspedisi'" :class="tab==='ekspedisi' ? activeTab : inactiveTab" class="px-4 py-2 rounded-xl text-sm font-semibold">Ekspedisi</button>
                <button @click="tab='tracking'" :class="tab==='tracking' ? activeTab : inactiveTab" class="px-4 py-2 rounded-xl text-sm font-semibold">Tracking Aktif</button>
            </div>
        </div>
    </div>

    <template x-if="tab==='ekspedisi'">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Ekspedisi</h2>
                <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold" @click="openEkspedisiModal('create')">+ Tambah Ekspedisi</button>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Layanan</th><th class="px-4 py-3">Estimasi</th><th class="px-4 py-3">Ongkir Flat</th><th class="px-4 py-3">Aktif</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($ekspedisi as $item)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3 font-medium">{{ $item->nama_ekspedisi }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_layanan }}</td>
                                    <td class="px-4 py-3">{{ $item->estimasi_hari ?? '-' }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($item->ongkir_flat ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">@if($item->is_active)<span class="px-2 py-1 rounded-full bg-green-100 text-green-700">Aktif</span>@else<span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>@endif</td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <button class="text-blue-600" @click='openEkspedisiModal("edit", @json($item))'>Edit</button>
                                        <form method="POST" action="{{ route('admin.shipping.ekspedisi.toggle', $item->ekspedisi_id) }}">@csrf @method('PUT')<button class="text-amber-600" type="submit">Toggle</button></form>
                                        <form method="POST" action="{{ route('admin.shipping.ekspedisi.destroy', $item->ekspedisi_id) }}" onsubmit="return confirm('Hapus ekspedisi ini?')">@csrf @method('DELETE')<button class="text-red-600" type="submit">Hapus</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada ekspedisi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    <template x-if="tab==='tracking'">
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="flex gap-3 flex-wrap items-end">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi / customer" class="rounded-xl border px-4 py-2 text-sm min-w-[280px]">
                    <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold" type="submit">Filter</button>
                </form>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs uppercase text-slate-600"><th class="px-4 py-3">Kode Transaksi</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Ekspedisi</th><th class="px-4 py-3">No Resi</th><th class="px-4 py-3">Status Pesanan</th><th class="px-4 py-3">Estimasi Tiba</th><th class="px-4 py-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($trackingAktif as $row)
                                <tr class="border-t border-slate-100 align-top">
                                    <td class="px-4 py-3 font-mono">{{ $row->transaksi?->kode_transaksi }}</td>
                                    <td class="px-4 py-3">{{ $row->transaksi?->pengguna?->nama_pengguna ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->ekspedisi?->nama_ekspedisi ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->no_resi ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->status_pesanan }}</td>
                                    <td class="px-4 py-3">{{ $row->estimasi_tiba ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <details class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                                            <summary class="cursor-pointer text-blue-600 font-semibold">Kelola</summary>
                                            <div class="mt-3 space-y-3">
                                                <form method="POST" action="{{ route('admin.shipping.update-resi') }}" class="space-y-2">@csrf
                                                    <input type="hidden" name="pesanan_id" value="{{ $row->pesanan_id }}">
                                                    <input type="text" name="no_resi" value="{{ $row->no_resi }}" placeholder="No resi" class="w-full rounded-xl border px-3 py-2 text-sm">
                                                    <button class="w-full rounded-xl bg-slate-900 px-3 py-2 text-white text-sm font-semibold">Update Resi</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.shipping.update-status') }}" class="space-y-2">@csrf
                                                    <input type="hidden" name="pesanan_id" value="{{ $row->pesanan_id }}">
                                                    <select name="status_pesanan" class="w-full rounded-xl border px-3 py-2 text-sm">
                                                        @foreach(['menunggu_konfirmasi','dikemas','siap_kirim','diserahkan_ke_kurir','dalam_pengiriman','tiba_di_tujuan','diterima','bermasalah'] as $status)
                                                            <option value="{{ $status }}" @selected($row->status_pesanan === $status)>{{ $status }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" name="lokasi" placeholder="Lokasi" class="w-full rounded-xl border px-3 py-2 text-sm">
                                                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi update" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
                                                    <button class="w-full rounded-xl bg-[#2B9BAF] px-3 py-2 text-white text-sm font-semibold">Update Status</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.shipping.tracking-log.store') }}" class="space-y-2">@csrf
                                                    <input type="hidden" name="pesanan_id" value="{{ $row->pesanan_id }}">
                                                    <input type="text" name="lokasi" placeholder="Lokasi manual" class="w-full rounded-xl border px-3 py-2 text-sm">
                                                    <textarea name="deskripsi" rows="2" placeholder="Tambah tracking manual" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea>
                                                    <button class="w-full rounded-xl bg-amber-500 px-3 py-2 text-white text-sm font-semibold">Tambah Log</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Tidak ada tracking aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">{{ $trackingAktif->links() }}</div>
            </div>
        </div>
    </template>

    <div x-show="ekspedisiModal" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" style="display:none;">
        <div class="bg-white rounded-3xl p-6 w-full max-w-xl shadow-xl">
            <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold" x-text="ekspedisiMode==='create' ? 'Tambah Ekspedisi' : 'Edit Ekspedisi'"></h3><button class="text-slate-500" @click="ekspedisiModal=false">✕</button></div>
            <form :action="ekspedisiAction" method="POST" class="grid sm:grid-cols-2 gap-4">@csrf<input type="hidden" name="_method" :value="ekspedisiMethod"><div><label class="text-sm font-semibold">Nama</label><input name="nama_ekspedisi" x-model="ekspedisiForm.nama_ekspedisi" class="w-full rounded-xl border px-4 py-2"></div><div><label class="text-sm font-semibold">Layanan</label><input name="jenis_layanan" x-model="ekspedisiForm.jenis_layanan" class="w-full rounded-xl border px-4 py-2"></div><div><label class="text-sm font-semibold">Estimasi</label><input name="estimasi_hari" x-model="ekspedisiForm.estimasi_hari" class="w-full rounded-xl border px-4 py-2"></div><div><label class="text-sm font-semibold">Ongkir Flat</label><input type="number" step="0.01" name="ongkir_flat" x-model="ekspedisiForm.ongkir_flat" class="w-full rounded-xl border px-4 py-2"></div><div><label class="text-sm font-semibold">Logo URL</label><input name="logo_url" x-model="ekspedisiForm.logo_url" class="w-full rounded-xl border px-4 py-2"></div><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" x-model="ekspedisiForm.is_active"> Aktif</label><div class="sm:col-span-2 flex justify-end gap-2"><button type="button" @click="ekspedisiModal=false" class="px-4 py-2 border rounded-xl">Batal</button><button class="px-4 py-2 rounded-xl bg-[#2B9BAF] text-white">Simpan</button></div></form>
        </div>
    </div>
</div>

<script>
function shippingPage() {
    return {
        tab: 'ekspedisi',
        ekspedisiModal: false,
        ekspedisiMode: 'create',
        ekspedisiAction: '{{ route('admin.shipping.ekspedisi.store') }}',
        ekspedisiMethod: 'POST',
        ekspedisiForm: { nama_ekspedisi:'', jenis_layanan:'', estimasi_hari:'', ongkir_flat:'', logo_url:'', is_active:true },
        activeTab: 'bg-[#2B9BAF] text-white',
        inactiveTab: 'bg-slate-100 text-slate-700',
        openEkspedisiModal(mode, item = {}) {
            this.ekspedisiMode = mode;
            this.ekspedisiModal = true;
            this.ekspedisiAction = mode === 'create' ? '{{ route('admin.shipping.ekspedisi.store') }}' : '{{ route('admin.shipping.ekspedisi.update', ['id' => '__ID__']) }}'.replace('__ID__', item.ekspedisi_id);
            this.ekspedisiMethod = mode === 'create' ? 'POST' : 'PUT';
            this.ekspedisiForm = {
                nama_ekspedisi: item.nama_ekspedisi ?? '',
                jenis_layanan: item.jenis_layanan ?? '',
                estimasi_hari: item.estimasi_hari ?? '',
                ongkir_flat: item.ongkir_flat ?? '',
                logo_url: item.logo_url ?? '',
                is_active: !!item.is_active,
            };
        },
    }
}
</script>
@endsection

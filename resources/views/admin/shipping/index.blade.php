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
                            <tr class="text-left text-xs uppercase text-slate-600">
                                <th class="px-4 py-3">Kode Transaksi</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Ekspedisi</th>
                                <th class="px-4 py-3">No Resi</th>
                                <th class="px-4 py-3">Status Pesanan</th>
                                <th class="px-4 py-3">Estimasi Tiba</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trackingAktif as $pesanan)
                                @php
                                    $statusConfig = [
                                        'menunggu_konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                        'dikemas' => ['label' => 'Dikemas', 'class' => 'bg-purple-50 text-purple-600 border-purple-100'],
                                        'siap_kirim' => ['label' => 'Siap Kirim', 'class' => 'bg-sky-50 text-sky-600 border-sky-100'],
                                        'diserahkan_ke_kurir' => ['label' => 'Diserahkan ke Kurir', 'class' => 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                                        'dalam_pengiriman' => ['label' => 'Dalam Pengiriman', 'class' => 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                                        'dikirim' => ['label' => 'Dikirim', 'class' => 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                                        'tiba_di_tujuan' => ['label' => 'Tiba di Tujuan', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                        'diterima' => ['label' => 'Diterima', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                        'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                        'bermasalah' => ['label' => 'Bermasalah', 'class' => 'bg-red-50 text-red-600 border-red-100'],
                                    ];
                                    $statusInfo = $statusConfig[$pesanan->status_pesanan] ?? [
                                        'label' => ucfirst(str_replace('_', ' ', $pesanan->status_pesanan ?? '-')),
                                        'class' => 'bg-gray-50 text-gray-500 border-gray-200',
                                    ];
                                @endphp
                                <tr class="border-t border-slate-100 align-top">
                                    <td class="px-4 py-3 font-mono">{{ $pesanan->transaksi?->kode_transaksi }}</td>
                                    <td class="px-4 py-3">{{ $pesanan->transaksi?->buyer?->pengguna?->nama_pengguna ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $pesanan->ekspedisi?->nama_ekspedisi ?? '-' }}</td>
                                    <td class="px-4 py-3 font-mono text-sm">
                                        <span :id="'resi-display-{{ $pesanan->pesanan_id }}'">
                                            {{ $pesanan->no_resi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :id="'status-display-{{ $pesanan->pesanan_id }}'" 
                                              class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $statusInfo['class'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $pesanan->estimasi_tiba ?? '-' }}</td>
                                    <td class="px-4 py-3 min-w-[280px]">
                                        <div class="flex flex-col gap-2">
                                            
                                            {{-- Tombol trigger --}}
                                            <div class="flex items-center gap-3">
                                                {{-- Tombol Resi --}}
                                                <button @click="toggleResi({{ $pesanan->pesanan_id }}, '{{ $pesanan->no_resi ?? '' }}')"
                                                        :class="openResi === {{ $pesanan->pesanan_id }}
                                                        ? 'bg-blue-500 text-white' 
                                                        : 'bg-blue-50 text-blue-600 hover:bg-blue-100'"
                                                        class="flex items-center gap-1.5 px-3 py-2 
                                                        rounded-xl text-xs font-bold transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" 
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" 
                                                              stroke-width="2"
                                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                    Resi
                                                </button>

                                                {{-- Tombol Status (dropdown) --}}
                                                <div class="relative">
                                                    <button @click="toggleStatus({{ $pesanan->pesanan_id }}, '{{ $pesanan->status_pesanan }}')"
                                                            :class="openStatus === {{ $pesanan->pesanan_id }}
                                                            ? 'bg-purple-500 text-white' 
                                                            : 'bg-purple-50 text-purple-600 hover:bg-purple-100'"
                                                            class="flex items-center gap-1.5 px-3 py-2 
                                                            rounded-xl text-xs font-bold transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" 
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                                  stroke-width="2"
                                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        <span>Status</span>
                                                        <svg class="w-3 h-3 opacity-80" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4" />
                                                        </svg>
                                                    </button>

                                                    <!-- Dropdown menu -->
                                                    <div x-show="openStatus === {{ $pesanan->pesanan_id }}"
                                                         x-cloak
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                                         x-transition:enter-end="opacity-100 translate-y-0"
                                                         class="absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-md shadow-md z-50 p-1" style="display:none;">
                                                        <div class="py-1">
                                                            <button @click.prevent="saveStatusOption({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }}, 'dalam_pengiriman')"
                                                                    class="block w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded">
                                                                Dikirim
                                                            </button>
                                                            <button @click.prevent="saveStatusOption({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }}, 'diterima')"
                                                                    class="block w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded mt-0.5">
                                                                Selesai
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Panel Input Resi --}}
                                            <div x-show="openResi === {{ $pesanan->pesanan_id }}"
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="bg-blue-50 border border-blue-200 
                                                        rounded-xl p-3 space-y-2">
                                                
                                                <p class="text-xs font-bold text-blue-700">
                                                    📦 Update Nomor Resi
                                                </p>
                                                
                                                <input type="text"
                                                       x-model="resiValue[{{ $pesanan->pesanan_id }}]"
                                                       @keyup.enter="saveResi({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }})"
                                                       placeholder="Masukkan nomor resi..."
                                                       class="w-full px-3 py-2 rounded-lg border border-blue-200 
                                                              focus:border-blue-400 focus:outline-none 
                                                              text-xs font-mono bg-white transition">
                                                
                                                <p class="text-[10px] text-blue-500">
                                                    Menyimpan resi otomatis ubah status ke <strong>Dikirim</strong>
                                                </p>
                                                
                                                {{-- TOMBOL UPDATE + BATAL --}}
                                                <div class="flex gap-2">
                                                    <button @click="saveResi({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }})"
                                                            :disabled="savingResi === {{ $pesanan->pesanan_id }}"
                                                            class="flex-[2] py-2 bg-blue-500 text-white 
                                                                   rounded-lg text-xs font-bold 
                                                                   hover:bg-blue-600 transition 
                                                                   disabled:opacity-60 
                                                                   flex items-center justify-center gap-1.5">
                                                        <svg x-show="savingResi === {{ $pesanan->pesanan_id }}" 
                                                             class="animate-spin w-3 h-3" 
                                                             fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" 
                                                                    stroke="currentColor" stroke-width="4"/>
                                                            <path class="opacity-75" fill="currentColor"
                                                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                        </svg>
                                                        <svg x-show="savingResi !== {{ $pesanan->pesanan_id }}" 
                                                             class="w-3 h-3" fill="none" stroke="currentColor" 
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                                  stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span x-text="savingResi === {{ $pesanan->pesanan_id }} 
                                                            ? 'Menyimpan...' : 'Update Resi'">
                                                        </span>
                                                    </button>
                                                    <button @click="openResi = null"
                                                            class="flex-1 py-2 bg-white border border-gray-200 
                                                                   text-gray-500 rounded-lg text-xs 
                                                                   font-semibold hover:bg-gray-50 transition">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Panel Input Status removed: replaced by compact dropdown options --}}

                                        </div>
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
        
        openResi: null,
        openStatus: null,
        resiValue: {},
        statusValue: {},
        savingResi: null,
        savingStatus: null,
        
        toggleResi(id, currentResi) {
            if (this.openResi === id) {
                this.openResi = null;
            } else {
                this.openResi = id;
                this.openStatus = null;
                if (!this.resiValue[id]) {
                    this.resiValue[id] = currentResi || '';
                }
            }
        },
        
        toggleStatus(id, currentStatus) {
            if (this.openStatus === id) {
                this.openStatus = null;
            } else {
                this.openStatus = id;
                this.openResi = null;
                if (!this.statusValue[id]) {
                    this.statusValue[id] = currentStatus || '';
                }
            }
        },
        
        async saveResi(pesananId, transaksiId) {
            const resi = this.resiValue[pesananId];
            if (!resi || !resi.trim()) {
                alert('Masukkan nomor resi terlebih dahulu!');
                return;
            }
            this.savingResi = pesananId;
            try {
                const res = await fetch(
                    '/admin/shipping/' + pesananId + '/update-resi', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ 
                        no_resi: resi,
                        transaksi_id: transaksiId
                    })
                });
                let data;
                try {
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        data = await res.json();
                    } else {
                        const text = await res.text();
                        throw new Error('Non-JSON response: ' + text);
                    }
                } catch(parseErr) {
                    throw parseErr;
                }
                    if (data.success) {
                    this.openResi = null;
                    const el = document.getElementById('resi-display-' + pesananId);
                    if (el) el.textContent = resi;
                    const statusEl = document.getElementById(
                        'status-display-' + pesananId);
                    if (statusEl) {
                        // prefer new key 'dikirim' but support older 'dalam_pengiriman'
                        statusEl.textContent = 'Dikirim';
                        statusEl.className = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20';
                    }
                    if (window.showAdminToast) {
                        showAdminToast('✅ Resi berhasil disimpan!');
                    } else {
                        alert('✅ Resi berhasil disimpan!');
                    }
                } else {
                    alert(data.message || 'Gagal menyimpan resi');
                }
            } catch(e) {
                alert('Error: ' + e.message);
            } finally {
                this.savingResi = null;
            }
        },
        
        async saveStatus(pesananId, transaksiId) {
            const status = this.statusValue[pesananId];
            if (!status) {
                alert('Pilih status terlebih dahulu!');
                return;
            }
            this.savingStatus = pesananId;
            try {
                const res = await fetch(
                    '/admin/shipping/' + pesananId + '/update-status', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ 
                        status: status,
                        transaksi_id: transaksiId
                    })
                });
                let data;
                try {
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        data = await res.json();
                    } else {
                        const text = await res.text();
                        throw new Error('Non-JSON response: ' + text);
                    }
                } catch(parseErr) {
                    throw parseErr;
                }
                if (data.success) {
                    this.openStatus = null;
                    const el = document.getElementById(
                        'status-display-' + pesananId);
                    if (el) {
                        const labels = {
                            'menunggu_konfirmasi': ['Menunggu', 'bg-amber-50 text-amber-600 border-amber-100'],
                            'dikemas':             ['Dikemas', 'bg-purple-50 text-purple-600 border-purple-100'],
                            'siap_kirim':          ['Siap Kirim', 'bg-sky-50 text-sky-600 border-sky-100'],
                            'diserahkan_ke_kurir': ['Ke Kurir', 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                            'dalam_pengiriman':    ['Dikirim', 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                            'dikirim':             ['Dikirim', 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                            'tiba_di_tujuan':      ['Tiba', 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                            'diterima':            ['Diterima', 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                            'selesai':             ['Selesai', 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                        };
                        const [label, cls] = labels[status] ?? [status, 'bg-gray-50 text-gray-500 border-gray-200'];
                        el.textContent = label;
                        el.className = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold ' + cls;
                    }
                    if (window.showAdminToast) {
                        showAdminToast('✅ Status pengiriman diperbarui!');
                    } else {
                        alert('✅ Status pengiriman diperbarui!');
                    }
                } else {
                    alert(data.message || 'Gagal update status');
                }
            } catch(e) {
                alert('Error: ' + e.message);
            } finally {
                this.savingStatus = null;
            }
        },

        async saveStatusOption(pesananId, transaksiId, status) {
            // Shortcut for dropdown options (Dikirim / Selesai)
            this.savingStatus = pesananId;
            try {
                const res = await fetch(
                    '/admin/shipping/' + pesananId + '/update-status', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ 
                        status: status,
                        transaksi_id: transaksiId
                    })
                });
                let data;
                try {
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        data = await res.json();
                    } else {
                        const text = await res.text();
                        throw new Error('Non-JSON response: ' + text);
                    }
                } catch(parseErr) {
                    throw parseErr;
                }
                if (data.success) {
                    this.openStatus = null;
                    const el = document.getElementById(
                        'status-display-' + pesananId);
                    if (el) {
                        const labels = {
                            'dalam_pengiriman': ['Dikirim', 'bg-[#63A2BB]/10 text-[#63A2BB] border-[#63A2BB]/20'],
                            'diterima': ['Selesai', 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                        };
                        const [label, cls] = labels[status] ?? [status, 'bg-gray-50 text-gray-500 border-gray-200'];
                        el.textContent = label;
                        el.className = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold ' + cls;
                    }
                    if (window.showAdminToast) {
                        showAdminToast('✅ Status pengiriman diperbarui!');
                    } else {
                        alert('✅ Status pengiriman diperbarui!');
                    }
                } else {
                    alert(data.message || 'Gagal update status');
                }
            } catch(e) {
                alert('Error: ' + e.message);
            } finally {
                this.savingStatus = null;
            }
        },
        
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

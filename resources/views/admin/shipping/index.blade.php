@extends('layouts.admin')

@section('title', 'Shipping Management')

@section('content')
<div style="padding: 28px 28px 40px;" x-data="shippingPage()">

    {{-- Page Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <p style="font-size:11px; font-weight:700; color:#63A2BB; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 4px;">Logistik</p>
            <h1 class="page-header-title" style="margin:0 0 4px;">Shipping Management</h1>
            <p class="page-header-sub" style="margin:0; color:#94A3B8;">Kelola opsi ekspedisi pengiriman dan update nomor resi / status tracking aktif.</p>
        </div>
        
        {{-- Elegant Tab Navigation --}}
        <div style="display:flex; background:white; padding:4px; border-radius:12px; border:1px solid #E2E8F0; gap:2px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <button @click="tab='ekspedisi'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='ekspedisi' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                🚚 Ekspedisi
            </button>
            <button @click="tab='tracking'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='tracking' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                📦 Tracking Aktif
            </button>
        </div>
    </div>

    {{-- ===== EKSPEDISI TAB ===== --}}
    <div x-show="tab==='ekspedisi'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 15px; font-weight: 800; color: #1E293B; margin: 0;">Metode Ekspedisi Terdaftar</h2>
                <button class="btn-primary" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;" @click="openEkspedisiModal('create')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Ekspedisi
                </button>
            </div>

            <div class="panel">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Ekspedisi</th>
                                <th>Jenis Layanan</th>
                                <th style="width: 140px; text-align: center;">Estimasi</th>
                                <th style="text-align: right; width: 160px;">Ongkos Kirim Flat</th>
                                <th style="text-align: center; width: 120px;">Status</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ekspedisi as $item)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if($item->logo_url)
                                                <img src="{{ $item->logo_url }}" alt="Logo" style="height: 24px; width: auto; max-width: 60px; object-fit: contain;">
                                            @else
                                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: #64748B; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px;">
                                                    {{ mb_strtoupper(mb_substr($item->nama_ekspedisi, 0, 2)) }}
                                                </div>
                                            @endif
                                            <span style="font-weight: 700; color: #0F172A;">{{ $item->nama_ekspedisi }}</span>
                                        </div>
                                    </td>
                                    <td style="font-weight: 500; color: #475569;">{{ $item->jenis_layanan }}</td>
                                    <td style="text-align: center; color: #475569; font-weight: 600;">
                                        {{ $item->estimasi_hari ? $item->estimasi_hari . ' Hari' : '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #0F172A; font-family: monospace;">
                                        Rp {{ number_format($item->ongkir_flat ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge" style="background:#E2E8F0; color:#64748B;">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" @click='openEkspedisiModal("edit", @json($item))'>
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Edit
                                            </button>
                                            
                                            <form method="POST" action="{{ route('admin.shipping.ekspedisi.toggle', $item->ekspedisi_id) }}" style="margin:0;">
                                                @csrf 
                                                @method('PUT')
                                                @if($item->is_active)
                                                    <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #D97706; border-color: #FDE68A; background: #FFFBEB; display: inline-flex; align-items: center; gap: 4px;" onmouseover="this.style.background='#FDE68A'" onmouseout="this.style.background='#FFFBEB'" type="submit">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5-6v6M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Nonaktifkan
                                                    </button>
                                                @else
                                                    <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #059669; border-color: #A7F3D0; background: #ECFDF5; display: inline-flex; align-items: center; gap: 4px;" onmouseover="this.style.background='#A7F3D0'" onmouseout="this.style.background='#ECFDF5'" type="submit">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Aktifkan
                                                    </button>
                                                @endif
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.shipping.ekspedisi.destroy', $item->ekspedisi_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ekspedisi ini secara permanen?')" style="margin:0;">
                                                @csrf 
                                                @method('DELETE')
                                                <button class="btn-danger" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" type="submit">
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada ekspedisi terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TRACKING TAB ===== --}}
    <div x-show="tab==='tracking'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            {{-- Filter Panel --}}
            <div class="panel" style="padding: 16px 20px;">
                <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
                    <div style="flex: 1; min-width: 280px;">
                        <label class="form-label" style="margin-bottom: 6px;">Pencarian Order</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi atau nama customer..." class="form-input" style="height: 38px;">
                    </div>
                    <button class="btn-primary" style="height: 38px; padding: 0 18px;" type="submit">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Filter
                    </button>
                    @if(request('search'))
                        <a href="?tab=tracking" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 14px;">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tracking Table --}}
            <div class="panel">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Kode Transaksi</th>
                                <th>Customer</th>
                                <th style="width: 130px;">Ekspedisi</th>
                                <th style="width: 150px;">Nomor Resi</th>
                                <th style="width: 160px; text-align: center;">Status Pengiriman</th>
                                <th style="width: 130px;">Estimasi Tiba</th>
                                <th style="width: 260px; text-align: center;">Manajemen Resi & Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trackingAktif as $pesanan)
                                @php
                                    $statusConfig = [
                                        'menunggu_konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'badge-warning'],
                                        'dikemas' => ['label' => 'Dikemas', 'class' => 'badge-admin'],
                                        'siap_kirim' => ['label' => 'Siap Kirim', 'class' => 'badge-info'],
                                        'diserahkan_ke_kurir' => ['label' => 'Diserahkan ke Kurir', 'class' => 'badge-info'],
                                        'dalam_pengiriman' => ['label' => 'Dalam Pengiriman', 'class' => 'badge-info'],
                                        'dikirim' => ['label' => 'Dikirim', 'class' => 'badge-info'],
                                        'tiba_di_tujuan' => ['label' => 'Tiba di Tujuan', 'class' => 'badge-success'],
                                        'diterima' => ['label' => 'Diterima', 'class' => 'badge-success'],
                                        'selesai' => ['label' => 'Selesai', 'class' => 'badge-success'],
                                        'bermasalah' => ['label' => 'Bermasalah', 'class' => 'badge-danger'],
                                    ];
                                    $statusInfo = $statusConfig[$pesanan->status_pesanan] ?? [
                                        'label' => ucfirst(str_replace('_', ' ', $pesanan->status_pesanan ?? '-')),
                                        'class' => 'badge',
                                    ];
                                @endphp
                                <tr>
                                    <td style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 13px;">{{ $pesanan->transaksi?->kode_transaksi }}</td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">{{ $pesanan->transaksi?->buyer?->pengguna?->nama_pengguna ?? '-' }}</div>
                                    </td>
                                    <td style="font-weight: 500; color: #475569;">{{ $pesanan->ekspedisi?->nama_ekspedisi ?? '-' }}</td>
                                    <td style="font-family: monospace; font-size: 13px; font-weight: 600; color: #1E293B;">
                                        <span id="resi-display-{{ $pesanan->pesanan_id }}">
                                            {{ $pesanan->no_resi ?: '-' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span id="status-display-{{ $pesanan->pesanan_id }}" class="badge {{ $statusInfo['class'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td style="color: #64748B; font-size: 12.5px;">{{ $pesanan->estimasi_tiba ?? '-' }}</td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 6px;">
                                            
                                            {{-- Action Buttons --}}
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                
                                                {{-- Resi Edit Trigger --}}
                                                <button @click="toggleResi({{ $pesanan->pesanan_id }}, '{{ $pesanan->no_resi ?? '' }}')"
                                                        :style="openResi === {{ $pesanan->pesanan_id }}
                                                        ? 'background:#63A2BB; color:white;' 
                                                        : ''"
                                                        class="btn-secondary"
                                                        style="padding: 6px 12px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; font-weight: 700;">
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    Input Resi
                                                </button>

                                                {{-- Status Dropdown Trigger --}}
                                                <div style="position: relative;">
                                                    <button @click="toggleStatus({{ $pesanan->pesanan_id }}, '{{ $pesanan->status_pesanan }}')"
                                                            :style="openStatus === {{ $pesanan->pesanan_id }}
                                                            ? 'background:#7C3AED; color:white; border-color:#7C3AED;' 
                                                            : 'color:#7C3AED; border-color:#F5F3FF; background:#F5F3FF;'"
                                                            class="btn-secondary"
                                                            style="padding: 6px 12px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px; font-weight: 700;"
                                                            onmouseover="if(openStatus !== {{ $pesanan->pesanan_id }})this.style.background='#EDE9FE'"
                                                            onmouseout="if(openStatus !== {{ $pesanan->pesanan_id }})this.style.background='#F5F3FF'">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        Status
                                                        <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 8l4 4 4-4"/></svg>
                                                    </button>

                                                    <!-- Dropdown menu -->
                                                    <div x-show="openStatus === {{ $pesanan->pesanan_id }}"
                                                         x-cloak
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                                         x-transition:enter-end="opacity-100 translate-y-0"
                                                         style="position: absolute; right: 0; margin-top: 6px; width: 150px; background: white; border: 1px solid #E2E8F0; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); z-index: 50; padding: 4px;" @click.outside="openStatus = null">
                                                        <button @click.prevent="saveStatusOption({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }}, 'dalam_pengiriman')"
                                                                style="width: 100%; text-align: left; padding: 8px 12px; font-size: 12.5px; font-weight: 600; color: #334155; border: none; background: transparent; cursor: pointer; border-radius: 6px; transition: background 0.1s;"
                                                                onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                                            🚚 Kirimkan / OTW
                                                        </button>
                                                        <button @click.prevent="saveStatusOption({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }}, 'diterima')"
                                                                style="width: 100%; text-align: left; padding: 8px 12px; font-size: 12.5px; font-weight: 600; color: #059669; border: none; background: transparent; cursor: pointer; border-radius: 6px; transition: background 0.1s;"
                                                                onmouseover="this.style.background='#ECFDF5'" onmouseout="this.style.background='transparent'">
                                                            ✓ Selesaikan Order
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Resi Input Sub-Panel --}}
                                            <div x-show="openResi === {{ $pesanan->pesanan_id }}"
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 style="background: #EFF8FB; border: 1.5px solid rgba(99,162,187,0.2); border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
                                                
                                                <span style="font-size: 10.5px; font-weight: 700; color: #4A8BA3;">📦 MASUKKAN NO RESI EKSPEDISI</span>
                                                
                                                <input type="text"
                                                       x-model="resiValue[{{ $pesanan->pesanan_id }}]"
                                                       @keyup.enter="saveResi({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }})"
                                                       placeholder="Contoh: JNE12345678..."
                                                       class="form-input"
                                                       style="height: 32px; font-size: 12px; font-family: monospace; font-weight: 600; padding: 0 10px;">
                                                
                                                <p style="font-size: 9.5px; color: #63A2BB; margin: 0;">Menyimpan resi akan mengubah status menjadi <strong>Dikirim</strong>.</p>
                                                
                                                <div style="display: flex; gap: 6px; margin-top: 4px;">
                                                    <button @click="saveResi({{ $pesanan->pesanan_id }}, {{ $pesanan->transaksi_id }})"
                                                            :disabled="savingResi === {{ $pesanan->pesanan_id }}"
                                                            class="btn-primary"
                                                            style="flex: 2; height: 28px; font-size: 11px; padding: 0; justify-content: center;">
                                                        <svg x-show="savingResi === {{ $pesanan->pesanan_id }}" class="animate-spin" width="10" height="10" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"/></svg>
                                                        <span x-text="savingResi === {{ $pesanan->pesanan_id }} ? 'Menyimpan...' : 'Simpan Resi'"></span>
                                                    </button>
                                                    <button @click="openResi = null"
                                                            class="btn-secondary"
                                                            style="flex: 1; height: 28px; font-size: 11px; padding: 0; justify-content: center;">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #94A3B8; padding: 36px;">Tidak ada tracking pengiriman aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if(method_exists($trackingAktif ?? null, 'links') && $trackingAktif->hasPages())
                <div style="display: flex; justify-content: center; margin-top: 20px;">
                    {{ $trackingAktif->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- Ekspedisi Create/Edit Modal --}}
    <div x-show="ekspedisiModal" x-cloak style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;" @click.self="ekspedisiModal=false">
        <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 500px; overflow: hidden; border: 1px solid #E2E8F0;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;" x-text="ekspedisiMode==='create' ? 'Tambah Ekspedisi Baru' : 'Edit Opsi Ekspedisi'"></h3>
                <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" @click="ekspedisiModal=false">✕</button>
            </div>

            <form :action="ekspedisiAction" method="POST" style="padding: 20px 24px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
                @csrf
                <input type="hidden" name="_method" :value="ekspedisiMethod">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Nama Ekspedisi <span style="color: #EF4444;">*</span></label>
                        <input name="nama_ekspedisi" x-model="ekspedisiForm.nama_ekspedisi" required placeholder="Contoh: JNE, J&T, SiCepat" class="form-input" style="height: 38px;">
                    </div>
                    <div>
                        <label class="form-label">Jenis Layanan <span style="color: #EF4444;">*</span></label>
                        <input name="jenis_layanan" x-model="ekspedisiForm.jenis_layanan" required placeholder="Contoh: Reguler, YES, Cargo" class="form-input" style="height: 38px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Estimasi Pengiriman</label>
                        <input name="estimasi_hari" x-model="ekspedisiForm.estimasi_hari" placeholder="Contoh: 2-3" class="form-input" style="height: 38px;">
                    </div>
                    <div>
                        <label class="form-label">Ongkir Flat (Rupiah) <span style="color: #EF4444;">*</span></label>
                        <input type="number" step="0.01" min="0" name="ongkir_flat" x-model="ekspedisiForm.ongkir_flat" required class="form-input" style="height: 38px; font-family: monospace;">
                    </div>
                </div>

                <div>
                    <label class="form-label">Logo / Image URL</label>
                    <input name="logo_url" x-model="ekspedisiForm.logo_url" placeholder="Contoh: https://example.com/logo.png" class="form-input" style="height: 38px;">
                </div>

                <div style="padding: 4px 0;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #334155;">
                        <input type="checkbox" name="is_active" value="1" x-model="ekspedisiForm.is_active" style="width: 16px; height: 16px; accent-color: #63A2BB; cursor: pointer;">
                        Aktifkan opsi ekspedisi ini untuk buyer
                    </label>
                </div>

                <div style="margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                    <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" @click="ekspedisiModal=false">Batal</button>
                    <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Ekspedisi</button>
                </div>
            </form>
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
                        statusEl.textContent = 'Dikirim';
                        statusEl.className = 'badge badge-info';
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
                            'menunggu_konfirmasi': ['Menunggu', 'badge-warning'],
                            'dikemas':             ['Dikemas', 'badge-admin'],
                            'siap_kirim':          ['Siap Kirim', 'badge-info'],
                            'diserahkan_ke_kurir': ['Ke Kurir', 'badge-info'],
                            'dalam_pengiriman':    ['Dikirim', 'badge-info'],
                            'dikirim':             ['Dikirim', 'badge-info'],
                            'tiba_di_tujuan':      ['Tiba', 'badge-success'],
                            'diterima':            ['Diterima', 'badge-success'],
                            'selesai':             ['Selesai', 'badge-success'],
                        };
                        const [label, cls] = labels[status] ?? [status, 'badge'];
                        el.textContent = label;
                        el.className = 'badge ' + cls;
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
                            'dalam_pengiriman': ['Dikirim', 'badge-info'],
                            'diterima': ['Selesai', 'badge-success'],
                        };
                        const [label, cls] = labels[status] ?? [status, 'badge'];
                        el.textContent = label;
                        el.className = 'badge ' + cls;
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

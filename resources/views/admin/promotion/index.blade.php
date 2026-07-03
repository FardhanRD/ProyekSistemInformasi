@extends('layouts.admin')

@section('title', 'Promotion Management')

@section('content')
<div x-data="promotionPage()" style="padding: 28px 28px 40px; display: flex; flex-direction: column; gap: 24px;">
    
    {{-- Page Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div>
            <p style="font-size:11px; font-weight:700; color:#63A2BB; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 4px;">Pemasaran</p>
            <h1 class="page-header-title" style="margin:0 0 4px;">Promotion Management</h1>
            <p class="page-header-sub" style="margin:0; color:#94A3B8;">Kelola voucher, diskon produk, dan flash sale.</p>
        </div>
        
        {{-- Elegant Tab Navigation --}}
        <div style="display:flex; background:white; padding:4px; border-radius:12px; border:1px solid #E2E8F0; gap:2px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <button @click="tab='voucher'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='voucher' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                🎫 Voucher
            </button>
            <button @click="tab='diskon'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='diskon' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                🏷️ Diskon Produk
            </button>
            <button @click="tab='flash'" 
                class="border-none cursor-pointer text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-150"
                :class="tab==='flash' ? 'bg-[#63A2BB] text-white font-bold shadow-[0_2px_8px_rgba(99,162,187,0.3)]' : 'bg-transparent text-slate-500 hover:text-slate-800'">
                ⚡ Flash Sale
            </button>
        </div>
    </div>

    {{-- ===== VOUCHER TAB ===== --}}
    <div x-show="tab==='voucher'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 15px; font-weight: 800; color: #1E293B; margin: 0;">Voucher Toko Aktif</h2>
                <button class="btn-primary" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;" @click="openVoucherModal('create')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Voucher
                </button>
            </div>
            
            <div class="panel">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Kode Voucher</th>
                                <th>Nama Voucher</th>
                                <th>Jenis Diskon</th>
                                <th style="text-align: right; width: 140px;">Nilai Diskon</th>
                                <th style="text-align: right; width: 140px;">Min. Belanja</th>
                                <th style="text-align: center; width: 130px;">Kuota / Terpakai</th>
                                <th style="width: 160px;">Masa Berlaku</th>
                                <th style="text-align: center; width: 110px;">Status</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr>
                                    <td style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 13.5px;">{{ $voucher->kode_voucher }}</td>
                                    <td style="font-weight: 600; color: #334155;">{{ $voucher->nama_voucher }}</td>
                                    <td>
                                        <span class="badge badge-admin">{{ ucfirst($voucher->jenis_diskon) }}</span>
                                    </td>
                                    <td style="text-align: right; font-weight: 800; color: #63A2BB; font-family: monospace;">
                                        {{ $voucher->jenis_diskon === 'persen' ? $voucher->nilai_diskon.'%' : 'Rp '.number_format($voucher->nilai_diskon,0,',','.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #475569; font-family: monospace;">
                                        Rp {{ number_format($voucher->min_belanja ?? 0,0,',','.') }}
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #334155;">
                                        {{ $voucher->kuota ?? 'Unlimited' }} / <span style="color: #63A2BB;">{{ $voucher->kuota_terpakai ?? 0 }}</span>
                                    </td>
                                    <td style="font-size: 11px; color: #64748B; line-height: 1.4;">
                                        Mulai: {{ $voucher->berlaku_mulai }}<br>
                                        Selesai: {{ $voucher->berlaku_sampai }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($voucher->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge" style="background: #F1F5F9; color: #64748B;">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" @click='openVoucherModal("edit", @json($voucher))'>
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.promotion.voucher.destroy', $voucher->voucher_id) }}" onsubmit="return confirm('Hapus voucher ini?')" style="margin:0;">
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
                                    <td colspan="9" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada voucher terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Discounted Products Card --}}
            <div class="panel" style="margin-top: 12px;">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">🏷️ Produk dengan Harga Diskon</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Daftar varian produk yang memiliki harga promo di bawah harga modal dasarnya.</p>
                    </div>
                    <a href="{{ route('admin.master-product.index') }}" class="text-xs font-semibold text-admin hover:underline">Kelola Produk →</a>
                </div>

                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: right; width: 160px;">Harga Normal</th>
                                <th style="text-align: right; width: 160px;">Harga Diskon</th>
                                <th style="text-align: center; width: 120px;">Hemat</th>
                                <th style="text-align: center; width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produkDiskon ?? [] as $p)
                                @php
                                    $maxPersen = 0;
                                    $hargaNormal = 0;
                                    $hargaDiskon = 0;
                                    foreach ($p->detailProduk as $dp) {
                                        $orig = (float) $dp->harga;
                                        $efek = (float) $dp->harga_efektif;
                                        if ($orig > 0) {
                                            $p_saved = ($orig - $efek) / $orig * 100;
                                            if ($p_saved > $maxPersen) {
                                                $maxPersen = $p_saved;
                                                $hargaNormal = $orig;
                                                $hargaDiskon = $efek;
                                            }
                                        }
                                    }
                                    // If no active promo is found (fallback)
                                    if ($maxPersen <= 0 && $p->detailProduk->isNotEmpty()) {
                                        $first = $p->detailProduk->first();
                                        $hargaNormal = (float) $first->harga;
                                        $hargaDiskon = (float) $first->harga_efektif;
                                        $maxPersen = 0;
                                    }
                                    $persen = round($maxPersen);
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <img src="{{ $p->gambarUtama?->url_safe ?? asset('images/placeholder.png') }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid #E2E8F0;" alt="{{ $p->nama_produk }}">
                                            <div>
                                                <p style="font-weight: 700; color: #0F172A; margin: 0;">{{ $p->nama_produk }}</p>
                                                <p style="font-size: 11px; color: #94A3B8; margin: 2px 0 0;">Supplier: {{ $p->supplier->nama_toko ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: right; text-decoration: line-through; color: #94A3B8; font-family: monospace;">Rp {{ number_format($hargaNormal, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 800; color: #63A2BB; font-family: monospace;">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge" style="background: #FEE2E2; color: #EF4444;">-{{ $persen }}%</span>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($p->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge" style="background: #F1F5F9; color: #64748B;">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #94A3B8; padding: 28px;">Belum ada produk dengan harga diskon.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PROMO DISKON TAB ===== --}}
    <div x-show="tab==='diskon'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 15px; font-weight: 800; color: #1E293B; margin: 0;">Promo Diskon Produk</h2>
                <button class="btn-primary" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;" @click="openPromoModal('create', 'diskon_produk')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Promo
                </button>
            </div>
            
            <div class="panel">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Promo</th>
                                <th>Produk / Variant</th>
                                <th style="text-align: center; width: 120px;">Diskon (%)</th>
                                <th>Masa Berlaku</th>
                                <th style="text-align: center; width: 110px;">Status</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diskonProduk as $promo)
                                <tr>
                                    <td style="font-weight: 700; color: #0F172A;">{{ $promo->nama_promo }}</td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">
                                            {{ $promo->detailProduk?->produk?->nama_produk ?? $promo->produk?->nama_produk ?? '-' }}
                                        </div>
                                        @if($promo->detailProduk)
                                            <div style="font-size:11px; color:#63A2BB; margin-top:2px; font-weight:600;">
                                                Varian: {{ $promo->detailProduk?->warna?->nama_warna ?? '-' }} | Size: {{ $promo->detailProduk?->ukuran ?? '-' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 800; color: #EF4444; font-family: monospace;">{{ $promo->persen_diskon }}%</td>
                                    <td style="font-size: 11px; color: #64748B; line-height: 1.4;">
                                        Mulai: {{ $promo->mulai }}<br>
                                        Selesai: {{ $promo->selesai }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($promo->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge" style="background: #F1F5F9; color: #64748B;">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" @click='openPromoModal("edit", @json($promo), "diskon_produk")'>
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.promotion.promo.destroy', $promo->promo_id) }}" onsubmit="return confirm('Hapus promo ini?')" style="margin:0;">
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
                                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada diskon produk terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FLASH SALE TAB ===== --}}
    <div x-show="tab==='flash'" x-cloak x-transition>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 15px; font-weight: 800; color: #1E293B; margin: 0;">Flash Sale Terjadwal</h2>
                <button class="btn-primary" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px;" @click="openPromoModal('create', 'flash_sale')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Flash Sale
                </button>
            </div>
            
            <div class="panel">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Promo</th>
                                <th>Produk / Variant</th>
                                <th style="text-align: center; width: 110px;">Diskon (%)</th>
                                <th style="text-align: center; width: 120px;">Stok Flash</th>
                                <th>Masa Berlaku</th>
                                <th style="text-align: center; width: 110px;">Status</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flashSale as $promo)
                                <tr>
                                    <td style="font-weight: 700; color: #0F172A;">{{ $promo->nama_promo }}</td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">
                                            {{ $promo->detailProduk?->produk?->nama_produk ?? $promo->produk?->nama_produk ?? '-' }}
                                        </div>
                                        @if($promo->detailProduk)
                                            <div style="font-size:11px; color:#63A2BB; margin-top:2px; font-weight:600;">
                                                Varian: {{ $promo->detailProduk?->warna?->nama_warna ?? '-' }} | Size: {{ $promo->detailProduk?->ukuran ?? '-' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 800; color: #EF4444; font-family: monospace;">{{ $promo->persen_diskon }}%</td>
                                    <td style="text-align: center; font-weight: 700; color: #0F172A; font-family: monospace;">{{ $promo->stok_flash_sale ?? '-' }} pcs</td>
                                    <td style="font-size: 11px; color: #64748B; line-height: 1.4;">
                                        Mulai: {{ $promo->mulai }}<br>
                                        Selesai: {{ $promo->selesai }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if($promo->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge" style="background: #F1F5F9; color: #64748B;">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <button class="btn-secondary" style="padding: 6px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" @click='openPromoModal("edit", @json($promo), "flash_sale")'>
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.promotion.promo.destroy', $promo->promo_id) }}" onsubmit="return confirm('Hapus flash sale ini?')" style="margin:0;">
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
                                    <td colspan="7" style="text-align: center; color: #94A3B8; padding: 36px;">Belum ada promo flash sale terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== VOUCHER MODAL ===== --}}
    <div x-show="voucherModal" x-cloak style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;" @click.self="voucherModal=false">
        <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; border: 1px solid #E2E8F0;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;" x-text="voucherMode==='create' ? 'Tambah Voucher Baru' : 'Edit Detail Voucher'"></h3>
                <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" @click="voucherModal=false">✕</button>
            </div>

            <form :action="voucherAction" method="POST" style="padding: 20px 24px; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                @csrf
                <input type="hidden" name="_method" :value="voucherMethod">
                
                <div>
                    <label class="form-label">Kode Voucher <span style="color: #EF4444;">*</span></label>
                    <input name="kode_voucher" x-model="voucherForm.kode_voucher" required placeholder="Contoh: MERDEKA50" class="form-input" style="height: 38px; font-family: monospace; font-weight: 700; text-transform: uppercase;">
                </div>
                <div>
                    <label class="form-label">Nama Voucher <span style="color: #EF4444;">*</span></label>
                    <input name="nama_voucher" x-model="voucherForm.nama_voucher" required placeholder="Contoh: Voucher Diskon Kemerdekaan" class="form-input" style="height: 38px;">
                </div>
                <div>
                    <label class="form-label">Jenis Diskon</label>
                    <select name="jenis_diskon" x-model="voucherForm.jenis_diskon" class="form-input" style="height: 38px; cursor: pointer;">
                        <option value="persen">Persen (%)</option>
                        <option value="nominal">Nominal (Rupiah)</option>
                        <option value="ongkir">Gratis / Potongan Ongkir</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nilai Diskon <span style="color: #EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0" name="nilai_diskon" x-model="voucherForm.nilai_diskon" required class="form-input" style="height: 38px; font-family: monospace;">
                </div>
                <div>
                    <label class="form-label">Min Belanja</label>
                    <input type="number" step="0.01" min="0" name="min_belanja" x-model="voucherForm.min_belanja" class="form-input" style="height: 38px; font-family: monospace;">
                </div>
                <div>
                    <label class="form-label">Maks Diskon</label>
                    <input type="number" step="0.01" min="0" name="maks_diskon" x-model="voucherForm.maks_diskon" class="form-input" style="height: 38px; font-family: monospace;" placeholder="Kosongkan jika tidak dibatasi">
                </div>
                <div>
                    <label class="form-label">Kuota Penggunaan</label>
                    <input type="number" min="0" name="kuota" x-model="voucherForm.kuota" class="form-input" style="height: 38px;" placeholder="Kosongkan jika tak terbatas">
                </div>
                <div>&nbsp;</div>
                <div>
                    <label class="form-label">Berlaku Mulai</label>
                    <input type="datetime-local" name="berlaku_mulai" x-model="voucherForm.berlaku_mulai" class="form-input" style="height: 38px;">
                </div>
                <div>
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="datetime-local" name="berlaku_sampai" x-model="voucherForm.berlaku_sampai" class="form-input" style="height: 38px;">
                </div>
                
                <div style="grid-column: 1 / -1; padding: 4px 0;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #334155;">
                        <input type="checkbox" name="is_active" value="1" x-model="voucherForm.is_active" style="width: 16px; height: 16px; accent-color: #63A2BB; cursor: pointer;">
                        Aktifkan voucher ini agar dapat diklaim oleh pembeli
                    </label>
                </div>
                
                <div style="grid-column: 1 / -1; margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                    <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" @click="voucherModal=false">Batal</button>
                    <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Voucher</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== PROMO MODAL ===== --}}
    <div x-show="promoModal" x-cloak style="position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;" @click.self="promoModal=false">
        <div style="background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; border: 1px solid #E2E8F0;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;" x-text="promoMode==='create' ? 'Tambah Promo Baru' : 'Edit Detail Promo'"></h3>
                <button type="button" style="background: none; border: none; cursor: pointer; color: #94A3B8; font-size: 16px;" @click="promoModal=false">✕</button>
            </div>

            <form :action="promoAction" method="POST" style="padding: 20px 24px; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                @csrf
                <input type="hidden" name="_method" :value="promoMethod">
                
                <div>
                    <label class="form-label">Tipe Promosi</label>
                    <select name="jenis" x-model="promoForm.jenis" class="form-input" style="height: 38px; cursor: pointer;">
                        <option value="diskon_produk">Diskon Produk</option>
                        <option value="flash_sale">Flash Sale</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nama Promosi <span style="color: #EF4444;">*</span></label>
                    <input name="nama_promo" x-model="promoForm.nama_promo" required placeholder="Contoh: Diskon Gajian Akhir Bulan" class="form-input" style="height: 38px;">
                </div>
                
                <div>
                    <div>
                        <label class="form-label">Pilih Produk (Multi)</label>
                        <div class="flex items-center gap-2 mb-1">
                            <input type="text" placeholder="Cari produk..." x-model="promoSearchQuery" class="form-input" style="height: 34px;">
                            <button type="button" class="btn-secondary" @click="toggleAllPromoProducts()">Pilih Semua</button>
                        </div>
                        <div style="max-height:200px; overflow-y:auto; border:1px solid #E2E8F0; padding:4px;">
                            <template x-for="product in filteredPromoProducts()" :key="product.produk_id">
                                <div class="flex items-center">
                                    <input type="checkbox" name="produk_ids[]" :value="product.produk_id" x-model="promoSelectedIds" class="mr-2">
                                    <span x-text="product.nama_produk"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-label">Pilih Varian Spesifik (Opsional)</label>
                    <select name="detail_produk_id" x-model="promoForm.detail_produk_id" class="form-input" style="height: 38px; cursor: pointer;">
                        <option value="">Semua Varian Produk</option>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->detail_produk_id }}">{{ $variant->produk?->nama_produk }} — {{ $variant->warna?->nama_warna ?? '-' }} (Size: {{ $variant->ukuran ?? 'OS' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="form-label">Persen Diskon (%) <span style="color: #EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="persen_diskon" x-model="promoForm.persen_diskon" required class="form-input" style="height: 38px; font-family: monospace;">
                </div>
                <div>
                    <label class="form-label">Nominal Diskon (Rupiah)</label>
                    <input type="number" step="0.01" min="0" name="nominal_diskon" x-model="promoForm.nominal_diskon" class="form-input" style="height: 38px; font-family: monospace;" placeholder="Opsional">
                </div>
                
                <div x-show="promoForm.jenis === 'flash_sale'">
                    <label class="form-label">Stok Kuota Flash Sale <span style="color: #EF4444;">*</span></label>
                    <input type="number" min="0" name="stok_flash_sale" x-model="promoForm.stok_flash_sale" :required="promoForm.jenis === 'flash_sale'" class="form-input" style="height: 38px;" placeholder="Jumlah kuota unit flash sale">
                </div>
                <div x-show="promoForm.jenis === 'flash_sale'">&nbsp;</div>
                
                <div>
                    <label class="form-label">Mulai Tanggal & Jam</label>
                    <input type="datetime-local" name="mulai" x-model="promoForm.mulai" class="form-input" style="height: 38px;">
                </div>
                <div>
                    <label class="form-label">Selesai Tanggal & Jam</label>
                    <input type="datetime-local" name="selesai" x-model="promoForm.selesai" class="form-input" style="height: 38px;">
                </div>
                
                <div style="grid-column: 1 / -1; padding: 4px 0;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #334155;">
                        <input type="checkbox" name="is_active" value="1" x-model="promoForm.is_active" style="width: 16px; height: 16px; accent-color: #63A2BB; cursor: pointer;">
                        Aktifkan promosi ini agar langsung memotong harga jual
                    </label>
                </div>
                
                <div style="grid-column: 1 / -1; margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 14px;">
                    <button type="button" class="btn-secondary" style="height: 38px; padding: 0 16px;" @click="promoModal=false">Batal</button>
                    <button type="submit" class="btn-primary" style="height: 38px; padding: 0 20px;">Simpan Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function promotionPage() {
    return {
        tab: new URLSearchParams(window.location.search).get('tab') || 'voucher',
        voucherModal: false,
        promoModal: false,
        voucherMode: 'create',
        promoMode: 'create',
        voucherAction: '{{ route('admin.promotion.voucher.store') }}',
        voucherMethod: 'POST',
        promoAction: '{{ route('admin.promotion.promo.store') }}',
        promoMethod: 'POST',
        promoProducts: @json($products),
        promoSearchQuery: '',
        promoSelectedIds: [],
        
        voucherForm: { kode_voucher:'', nama_voucher:'', jenis_diskon:'persen', nilai_diskon:0, min_belanja:0, maks_diskon:'', kuota:'', berlaku_mulai:'', berlaku_sampai:'', is_active:true },
        promoForm: { jenis:'diskon_produk', nama_promo:'', produk_id:'', detail_produk_id:'', persen_diskon:0, nominal_diskon:'', stok_flash_sale:'', mulai:'', selesai:'', is_active:true },
        
        filteredPromoProducts() {
            return this.promoProducts.filter(p => p.nama_produk.toLowerCase().includes(this.promoSearchQuery.toLowerCase()));
        },
        toggleAllPromoProducts() {
            const ids = this.filteredPromoProducts().map(p => p.produk_id);
            const allSelected = ids.every(id => this.promoSelectedIds.includes(id));
            if(allSelected) {
                this.promoSelectedIds = this.promoSelectedIds.filter(id => !ids.includes(id));
            } else {
                this.promoSelectedIds = [...new Set([...this.promoSelectedIds, ...ids])];
            }
        },
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
                is_active: mode === 'create' ? true : !!voucher.is_active,
            };
        },
        openPromoModal(mode, promoType, promo = {}) {
            this.promoMode = mode;
            this.promoModal = true;
            this.promoAction = mode === 'create' ? '{{ route('admin.promotion.promo.store') }}' : '{{ route('admin.promotion.promo.update', ['id' => '__ID__']) }}'.replace('__ID__', promo.promo_id);
            this.promoMethod = mode === 'create' ? 'POST' : 'PUT';
            this.promoSelectedIds = promo.produk_ids ?? [];
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
                is_active: mode === 'create' ? true : !!promo.is_active,
            };
        },
    }
}
</script>
@endsection

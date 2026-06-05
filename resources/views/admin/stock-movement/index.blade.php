@extends('layouts.admin')

@section('title', 'Stock Movement Log')

@section('content')
<div style="padding: 32px; max-width: 1600px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Inventori</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Stock Movement Log</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Histori mutasi keluar masuk barang dan pencatatan penyesuaian (adjust) stok.</p>
        </div>
        <div>
            <a href="{{ route('admin.stock-movement.export', request()->query()) }}" class="btn-secondary" style="height: 40px; display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; font-weight: 600; font-size: 13px; border: 1.5px solid #E2E8F0;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 28px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);">
        <div class="panel-body" style="padding: 24px;">
            <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Jenis Gerak</label>
                    <select name="jenis" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Types (Semua)</option>
                        <option value="in" {{ ($jenis_filter ?? '') === 'in' ? 'selected' : '' }}>IN (Masuk)</option>
                        <option value="out" {{ ($jenis_filter ?? '') === 'out' ? 'selected' : '' }}>OUT (Keluar)</option>
                        <option value="adjustment" {{ ($jenis_filter ?? '') === 'adjustment' ? 'selected' : '' }}>ADJUSTMENT</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Filter per Produk</label>
                    <select name="produk_id" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Products (Semua Produk)</option>
                        @foreach($produk_list as $p)
                            <option value="{{ $p->produk_id }}" {{ ($produk_filter ?? '') == $p->produk_id ? 'selected' : '' }}>
                                {{ $p->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; background: #63A2BB;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request('start_date') || request('end_date') || request('jenis') || request('produk_id'))
                        <a href="{{ route('admin.stock-movement.index') }}" class="btn-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 10px; font-weight: 600; color: #64748B; border: 1.5px solid #E2E8F0; background: white;">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Movements Table Panel --}}
    <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 150px; padding: 16px 20px;">Waktu Mutasi</th>
                        <th style="padding: 16px 20px;">Nama Produk Varian</th>
                        <th style="width: 140px; padding: 16px 20px;">SKU</th>
                        <th style="width: 120px; text-align: center; padding: 16px 20px;">Tipe</th>
                        <th style="width: 90px; text-align: center; padding: 16px 20px;">Qty</th>
                        <th style="width: 90px; text-align: center; padding: 16px 20px;">Sebelum</th>
                        <th style="width: 90px; text-align: center; padding: 16px 20px;">Sesudah</th>
                        <th style="width: 130px; padding: 16px 20px;">Referensi</th>
                        <th style="padding: 16px 20px;">Keterangan / Catatan</th>
                        <th style="width: 140px; padding: 16px 20px;">Operator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        @php
                            $jenis_badge = match($m->jenis) {
                                'in' => 'badge-success',
                                'out' => 'badge-danger',
                                'adjustment' => 'badge-info',
                                default => 'badge'
                            };
                            $jenis_display = strtoupper($m->jenis);
                        @endphp
                        <tr>
                            <td style="font-family: monospace; color: #64748B; font-size: 12.5px; padding: 16px 20px; font-weight: 500;">
                                {{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td style="padding: 16px 20px;">
                                <div style="font-weight: 750; color: #0F172A; font-size: 14px;">{{ $m->detailProduk->produk->nama_produk ?? '-' }}</div>
                                <div style="font-size: 11px; color: #64748B; margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                    @if($m->detailProduk->warna)
                                        <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Warna: <strong>{{ $m->detailProduk->warna->nama_warna }}</strong></span>
                                    @endif
                                    @if($m->detailProduk->ukuran)
                                        <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Size: <strong>{{ $m->detailProduk->ukuran }}</strong></span>
                                    @endif
                                </div>
                            </td>
                            <td style="font-family: monospace; font-weight: 600; color: #475569; font-size: 13px; padding: 16px 20px;">{{ $m->detailProduk->sku ?? '-' }}</td>
                            <td style="text-align: center; padding: 16px 20px;">
                                <span class="badge {{ $jenis_badge }}" style="font-size: 10.5px; padding: 4px 10px; font-weight: 750;">{{ $jenis_display }}</span>
                            </td>
                            <td style="text-align: center; font-weight: 850; color: #0F172A; font-family: monospace; padding: 16px 20px; font-size: 14px;">
                                {{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}
                            </td>
                            <td style="text-align: center; color: #64748B; font-family: monospace; padding: 16px 20px; font-size: 13.5px;">{{ $m->stok_sebelum }}</td>
                            <td style="text-align: center; font-weight: 750; color: #1E293B; font-family: monospace; padding: 16px 20px; font-size: 13.5px;">{{ $m->stok_sesudah }}</td>
                            <td style="color: #475569; font-size: 12.5px; font-weight: 700; padding: 16px 20px;">{{ $m->referensi ?: '-' }}</td>
                            <td style="color: #64748B; font-size: 13px; padding: 16px 20px; font-weight: 500; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $m->catatan }}">{{ $m->catatan ?: '-' }}</td>
                            <td style="padding: 16px 20px;">
                                <span class="badge badge-admin" style="font-size: 11px; padding: 4px 10px;">{{ $m->operator->nama_pengguna ?? 'Sistem' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; color: #94A3B8; padding: 48px; font-size: 14px; font-weight: 500;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Belum ada log mutasi pergerakan stok barang.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($movements ?? null, 'links') && $movements->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 20px; display: flex; justify-content: center; background: white;">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

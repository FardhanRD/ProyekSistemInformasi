@extends('layouts.admin')

@section('title', 'Supplier Purchase Order')

@section('content')
<div style="padding: 32px; max-width: 1600px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Inventori & Suplai</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Supplier Order Management</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Buat dan kelola Purchase Order (PO) produk ke supplier serta track penerimaan stok fisik.</p>
        </div>
        <div>
            <a href="{{ route('admin.supplier-order.create') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 20px; border-radius: 10px; font-weight: 700; background: #63A2BB; box-shadow: 0 4px 12px rgba(99,162,187,0.3);">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat PO Baru
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 28px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);">
        <div class="panel-body" style="padding: 24px;">
            <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Filter Supplier</label>
                    <select name="supplier_id" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Suppliers (Semua)</option>
                        @foreach($supplier_list ?? [] as $supp)
                            <option value="{{ $supp->supplier_id }}" {{ ($supplier_filter ?? '') == $supp->supplier_id ? 'selected' : '' }}>
                                {{ $supp->nama_toko }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Status PO</label>
                    <select name="status" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Status (Semua)</option>
                        <option value="draft" {{ ($status_filter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="dikirim" {{ ($status_filter ?? '') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="diterima" {{ ($status_filter ?? '') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="dibatalkan" {{ ($status_filter ?? '') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div style="width: 150px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="width: 150px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; background: #63A2BB;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request('supplier_id') || request('status') || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.supplier-order.index') }}" class="btn-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 10px; font-weight: 600; color: #64748B; border: 1.5px solid #E2E8F0; background: white;">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Orders Table Panel --}}
    <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 150px; padding: 16px 20px;">Kode PO</th>
                        <th style="padding: 16px 20px;">Supplier / Nama Toko</th>
                        <th style="width: 130px; padding: 16px 20px;">Tanggal Order</th>
                        <th style="text-align: center; width: 110px; padding: 16px 20px;">Total Item</th>
                        <th style="text-align: right; width: 180px; padding: 16px 20px;">Total Harga Modal</th>
                        <th style="text-align: center; width: 130px; padding: 16px 20px;">Status PO</th>
                        <th style="padding: 16px 20px;">Dibuat Oleh</th>
                        <th style="text-align: center; width: 100px; padding: 16px 20px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $po)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 13.5px; padding: 16px 20px;">{{ $po->kode_order }}</td>
                            <td style="padding: 16px 20px;">
                                <div style="font-weight: 750; color: #334155; font-size: 14.5px;">{{ $po->supplier?->nama_toko ?? '-' }}</div>
                                <div style="font-size: 11px; color: #94A3B8; font-weight: 600; margin-top: 2px;">Owner: {{ $po->supplier?->nama_owner ?? '-' }}</div>
                            </td>
                            <td style="color: #64748B; font-size: 13px; padding: 16px 20px;">{{ $po->tanggal_order ? $po->tanggal_order->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: center; font-weight: 750; color: #475569; padding: 16px 20px; font-size: 13.5px;">{{ number_format($po->total_item) }} pcs</td>
                            <td style="text-align: right; font-weight: 850; color: #0F172A; font-family: monospace; padding: 16px 20px; font-size: 14.5px;">
                                Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                @php
                                    $statusClasses = [
                                        'draft' => 'badge',
                                        'dikirim' => 'badge-info',
                                        'diterima' => 'badge-success',
                                        'dibatalkan' => 'badge-danger',
                                    ];
                                    $st = strtolower($po->status);
                                    $badgeClass = $statusClasses[$st] ?? 'badge';
                                @endphp
                                <span class="badge {{ $badgeClass }}" style="font-size: 11px; padding: 4px 10px; font-weight: 750;">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td style="padding: 16px 20px;">
                                <span class="badge badge-admin" style="font-size: 11px; padding: 4px 10px;">{{ $po->admin?->pengguna?->nama_pengguna ?? 'Admin' }}</span>
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                <a href="{{ route('admin.supplier-order.show', $po->supplier_order_id) }}" 
                                   class="btn-secondary" 
                                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;"
                                   title="Lihat Detail PO">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94A3B8; padding: 48px; font-size: 14px; font-weight: 500;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Belum ada riwayat order PO supplier.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($orders ?? null, 'links') && $orders->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 20px; display: flex; justify-content: center; background: white;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

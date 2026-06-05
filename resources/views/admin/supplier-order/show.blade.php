@extends('layouts.admin')

@section('title', 'Detail Supplier Order')

@section('content')
<div style="padding: 32px; max-width: 1200px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Detail Purchase Order</p>
            <h1 class="page-header-title" style="margin: 0; font-family: monospace; font-size: 26px; font-weight: 800; tracking: -0.5px; color: #0F172A;">{{ $po->kode_order }}</h1>
            <p class="page-header-sub" style="margin: 6px 0 0; color: #64748B; font-size: 13.5px;">
                Tanggal PO: <strong style="color: #334155;">{{ $po->tanggal_order ? $po->tanggal_order->format('d F Y') : '-' }}</strong> | Supplier: <strong style="color: #334155;">{{ $po->supplier?->nama_toko }}</strong>
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-shrink: 0;">
            <a href="{{ route('admin.supplier-order.index') }}" class="btn-secondary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; font-size: 13px; font-weight: 600; border-radius: 10px;">← Kembali</a>
            <a href="{{ route('admin.supplier-order.invoice', $po->supplier_order_id) }}" target="_blank" class="btn-primary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 20px; font-size: 13px; font-weight: 700; border-radius: 10px; background: #0F172A; box-shadow: 0 4px 12px rgba(15,23,42,0.25);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right: 6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice
            </a>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Status Cards (Grid 3 Columns) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            
            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Status Transaksi PO</p>
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
                    <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 4px 12px; font-weight: 750;">
                        {{ ucfirst($po->status) }}
                    </span>
                </div>
                <div style="width: 46px; height: 46px; border-radius: 14px; background: #EFF8FB; display: flex; align-items: center; justify-content: center; border: 1.5px solid #DBEAFE; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="#63A2BB" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>

            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Total Kuantitas Item</p>
                    <p style="font-size: 22px; font-weight: 850; color: #1E293B; margin: 0; font-family: monospace;">{{ number_format($po->total_item) }} pcs</p>
                </div>
                <div style="width: 46px; height: 46px; border-radius: 14px; background: #F1F5F9; display: flex; align-items: center; justify-content: center; border: 1.5px solid #E2E8F0; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="#475569" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
                <div>
                    <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Total Harga Modal</p>
                    <p style="font-size: 22px; font-weight: 900; color: #63A2BB; margin: 0; font-family: monospace;">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</p>
                </div>
                <div style="width: 46px; height: 46px; border-radius: 14px; background: rgba(99,162,187,0.06); display: flex; align-items: center; justify-content: center; border: 1.5px solid rgba(99,162,187,0.15); flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="#63A2BB" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start;">
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                {{-- Detail Table Panel --}}
                <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); overflow: hidden; background: white;">
                    <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                        <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Daftar Item Detail PO</p>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="padding: 14px 24px;">Nama Produk Varian</th>
                                    <th style="text-align: center; width: 110px; padding: 14px 24px;">Kuantitas</th>
                                    <th style="text-align: right; width: 150px; padding: 14px 24px;">Harga Beli</th>
                                    <th style="text-align: right; width: 170px; padding: 14px 24px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($po->details ?? [] as $detail)
                                    <tr>
                                        <td style="padding: 16px 24px;">
                                            <div style="font-weight: 750; color: #0F172A; font-size: 14.5px;">{{ $detail->detailProduk?->produk?->nama_produk ?? '-' }}</div>
                                            <div style="font-size: 11px; color: #64748B; margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                                <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">SKU: {{ $detail->detailProduk?->sku ?: '-' }}</span>
                                                @if($detail->detailProduk?->warna?->nama_warna)
                                                    <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Warna: {{ $detail->detailProduk->warna->nama_warna }}</span>
                                                @endif
                                                @if($detail->detailProduk?->ukuran)
                                                    <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Size: {{ $detail->detailProduk->ukuran }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="text-align: center; font-weight: 750; color: #475569; padding: 16px 24px; font-size: 13.5px;">{{ number_format($detail->qty) }} pcs</td>
                                        <td style="text-align: right; color: #475569; font-family: monospace; padding: 16px 24px; font-size: 13.5px;">Rp {{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}</td>
                                        <td style="text-align: right; font-weight: 850; color: #63A2BB; font-family: monospace; padding: 16px 24px; font-size: 14px;">
                                            Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #94A3B8; padding: 32px; font-size: 13.5px;">Tidak ada item detail PO.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan Panel --}}
                @if($po->catatan)
                    <div class="panel" style="border-radius: 16px; border: 1px solid rgba(99,162,187,0.15); box-shadow: 0 4px 20px -2px rgba(99, 162, 187, 0.05); background: rgba(99,162,187,0.03); overflow: hidden;">
                        <div class="panel-header" style="padding: 16px 24px; border-bottom: 1px solid rgba(99,162,187,0.1); background: none;">
                            <p class="panel-title" style="color: #4A8BA3; font-size: 14px; font-weight: 800;">Catatan PO</p>
                        </div>
                        <div class="panel-body" style="padding: 20px; font-size: 13.5px; color: #334155; line-height: 1.6; font-weight: 500;">
                            {!! nl2br(e($po->catatan)) !!}
                        </div>
                    </div>
                @endif
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                {{-- Supplier Info Panel --}}
                <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                    <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                        <p class="panel-title" style="font-size: 14px; font-weight: 800; color: #1E293B;">Informasi Supplier</p>
                    </div>
                    <div class="panel-body" style="padding: 20px; font-size: 13.5px; display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Nama Toko / Perusahaan</span>
                            <p style="margin: 4px 0 0; font-weight: 750; color: #1E293B; font-size: 14px;">{{ $po->supplier?->nama_toko ?? '-' }}</p>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Owner / Pemilik</span>
                            <p style="margin: 4px 0 0; color: #475569; font-weight: 500;">{{ $po->supplier?->nama_owner ?? '-' }}</p>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Nomor HP / Telepon</span>
                            <p style="margin: 4px 0 0; font-family: monospace; color: #475569; font-weight: 600;">{{ $po->supplier?->no_telepon ?? '-' }}</p>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Email</span>
                            <p style="margin: 4px 0 0; color: #475569; font-weight: 500;">{{ $po->supplier?->email ?? '-' }}</p>
                        </div>
                        <div style="border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 4px;">
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Alamat Supplier</span>
                            <p style="margin: 6px 0 0; color: #334155; line-height: 1.5; font-size: 13px; font-weight: 500; background: #F8FAFC; padding: 12px; border-radius: 10px; border: 1px solid #F1F5F9;">{{ $po->supplier?->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Operator Info Panel --}}
                <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                    <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                        <p class="panel-title" style="font-size: 14px; font-weight: 800; color: #1E293B;">Informasi Sistem</p>
                    </div>
                    <div class="panel-body" style="padding: 20px; font-size: 13.5px; display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Dibuat Oleh Admin</span>
                            <p style="margin: 4px 0 0; font-weight: 750; color: #1E293B; font-size: 14px;">{{ $po->admin?->pengguna?->nama_pengguna ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Actions --}}
        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1.5px solid #E2E8F0; padding-top: 24px; margin-top: 12px;">
            <a href="{{ route('admin.supplier-order.index') }}" class="btn-secondary" style="height: 42px; padding: 0 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 13.5px; font-weight: 600; border-radius: 10px;">← Kembali</a>
            
            @if($po->status === 'draft' || $po->status === 'dikirim')
                <form method="POST" action="{{ route('admin.supplier-order.receive', $po->supplier_order_id) }}" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menandai Purchase Order ini sudah diterima dan menambah stok secara otomatis?')">
                    @csrf
                    <button type="submit" class="btn-primary" style="height: 42px; padding: 0 24px; font-size: 13.5px; font-weight: 700; border-radius: 10px; background: #10B981; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.25); cursor: pointer;">
                        ✓ Tandai PO Diterima
                    </button>
                </form>
            @endif
        </div>

    </div>

</div>
@endsection

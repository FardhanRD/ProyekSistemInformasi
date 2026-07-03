@extends('layouts.admin')

@section('title', 'Detail Customer Order')

@section('content')
<div style="padding: 32px; max-width: 1400px; margin: 0 auto;" x-data="{ showProofModal: false, proofUrl: '', proofTitle: '', showRejectModal: false }">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Detail Transaksi</p>
            <h1 class="page-header-title" style="margin: 0; font-family: monospace; font-size: 26px; font-weight: 800; tracking: -0.5px; color: #0F172A;">{{ $order->kode_transaksi }}</h1>
            <p class="page-header-sub" style="margin: 6px 0 0; color: #64748B; font-size: 13.5px;">
                {{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('l, d F Y H:i') }} | Oleh: <strong style="color: #334155;">{{ $order->pengguna?->nama_pengguna }}</strong>
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-shrink: 0;">
            <a href="{{ route('admin.customer-order.index') }}" class="btn-secondary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; font-size: 13px; font-weight: 600; border-radius: 10px;">← Kembali</a>
            <a href="{{ route('admin.customer-order.invoice-pdf', $order->transaksi_id) }}" class="btn-primary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 20px; font-size: 13px; font-weight: 700; border-radius: 10px; background: #0F172A; box-shadow: 0 4px 12px rgba(15,23,42,0.25);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right: 6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Invoice PDF
            </a>
        </div>
    </div>

    {{-- Status Cards (Grid 3 Columns) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 28px;">
        
        {{-- Payment Status --}}
        <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Status Pembayaran</p>
                @php
                    $status_pembayaran = $order->pembayaran?->status_pembayaran ?? 'belum_ada';
                    $statusClasses = [
                        'menunggu_konfirmasi' => 'badge-warning',
                        'berhasil' => 'badge-success',
                        'gagal' => 'badge-danger',
                        'ditolak' => 'badge-danger',
                        'belum_ada' => 'badge',
                    ];
                    $badgeClass = $statusClasses[$status_pembayaran] ?? 'badge';
                @endphp
                <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 4px 12px; font-weight: 750;">
                    {{ ucfirst(str_replace('_', ' ', $status_pembayaran)) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 14px; background: #FFFDF5; display: flex; align-items: center; justify-content: center; border: 1.5px solid #FEF3C7; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="#D97706" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        {{-- Order Status --}}
        <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Status Pesanan</p>
                @php
                    $orderStatusClasses = [
                        'menunggu_pembayaran' => 'badge-warning',
                        'pembayaran_dikonfirmasi' => 'badge-info',
                        'diproses' => 'badge-admin',
                        'dikirim' => 'badge-info',
                        'selesai' => 'badge-success',
                        'dibatalkan' => 'badge-danger',
                    ];
                    $orderBadgeClass = $orderStatusClasses[$order->status] ?? 'badge';
                @endphp
                <span class="badge {{ $orderBadgeClass }}" style="font-size: 13px; padding: 4px 12px; font-weight: 750;">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 14px; background: #F0FDF4; display: flex; align-items: center; justify-content: center; border: 1.5px solid #DCFCE7; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="#16A34A" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        {{-- Total Order --}}
        <div class="panel" style="padding: 20px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); display: flex; align-items: center; justify-content: space-between; background: white;">
            <div>
                <p style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Total Pembayaran</p>
                <p style="font-size: 22px; font-weight: 900; color: #63A2BB; margin: 0; font-family: monospace;">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</p>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 14px; background: #EFF8FB; display: flex; align-items: center; justify-content: center; border: 1.5px solid #DBEAFE; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="#63A2BB" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start; margin-bottom: 28px;">
        
        <div style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Detail Items Panel --}}
            <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); overflow: hidden; background: white;">
                <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9; background: #FAFBCF; background: none;">
                    <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Daftar Item Pesanan</p>
                </div>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="padding: 14px 24px;">Produk / Detail</th>
                                <th style="text-align: center; width: 90px; padding: 14px 24px;">Qty</th>
                                <th style="text-align: right; width: 150px; padding: 14px 24px;">Harga Satuan</th>
                                <th style="text-align: right; width: 170px; padding: 14px 24px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->details ?? [] as $item)
                                <tr>
                                    <td style="padding: 16px 24px;">
                                        <div style="font-weight: 750; color: #0F172A; font-size: 14.5px;">{{ $item->produk?->nama_produk ?? '-' }}</div>
                                        <div style="font-size: 11px; color: #64748B; margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                            @if($item->warna)
                                                <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Warna: <strong>{{ $item->warna->nama_warna }}</strong></span>
                                            @endif
                                            @if($item->ukuran)
                                                <span style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Ukuran: <strong>{{ $item->ukuran }}</strong></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: #475569; padding: 16px 24px; font-size: 14px;">{{ $item->qty }}</td>
                                    <td style="text-align: right; color: #475569; font-family: monospace; padding: 16px 24px; font-size: 13.5px;">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 850; color: #0F172A; font-family: monospace; padding: 16px 24px; font-size: 14px;">
                                        Rp {{ number_format(($item->qty ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #94A3B8; padding: 32px; font-size: 13.5px;">Tidak ada item pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tracking Log Panel --}}
            <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                    <p class="panel-title" style="font-size: 15px; font-weight: 800; color: #1E293B;">Tracking & Pengiriman Logistik</p>
                </div>
                <div class="panel-body" style="padding: 24px;">
                    @if(!$pesanan)
                        <div style="padding: 32px; text-align: center; border: 2px dashed #E2E8F0; border-radius: 14px; background: #F8FAFC;">
                            <p style="color: #64748B; font-size: 13.5px; margin: 0; font-weight: 500;">Pesanan belum dikirim. Update tracking akan otomatis muncul setelah produk diserahkan kepada kurir pengiriman.</p>
                        </div>
                    @elseif(!$pesanan->trackingLogs || $pesanan->trackingLogs->isEmpty())
                        <div style="padding: 32px; text-align: center; border: 2px dashed #E2E8F0; border-radius: 14px; background: #F8FAFC;">
                            <p style="color: #64748B; font-size: 13.5px; margin: 0; font-weight: 500;">Belum ada riwayat update logistik untuk pengiriman ini.</p>
                        </div>
                    @else
                        <div style="position: relative; padding-left: 24px; border-left: 2px solid #E2E8F0; margin-left: 10px; display: flex; flex-direction: column; gap: 20px;">
                            @foreach($pesanan->trackingLogs as $log)
                                <div style="position: relative;">
                                    <div style="position: absolute; left: -31px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #63A2BB; border: 3px solid white; box-shadow: 0 0 0 2.5px #63A2BB;"></div>
                                    <div style="font-size: 11px; font-weight: 700; color: #94A3B8; margin-bottom: 4px; font-family: monospace;">
                                        {{ $log->waktu ? $log->waktu->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <p style="font-size: 14px; font-weight: 800; color: #1E293B; margin: 0 0 4px;">{{ $log->status }}</p>
                                    <p style="font-size: 12.5px; color: #64748B; margin: 0; line-height: 1.4; font-weight: 500;">{{ $log->catatan }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Buyer Details Panel --}}
            <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                    <p class="panel-title" style="font-size: 14px; font-weight: 800; color: #1E293B;">Informasi Pembeli</p>
                </div>
                <div class="panel-body" style="padding: 20px; font-size: 13.5px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Nama Lengkap</span>
                        <p style="margin: 4px 0 0; font-weight: 700; color: #1E293B; font-size: 14px;">{{ $order->pengguna?->nama_pengguna ?? '-' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Email Akun</span>
                        <p style="margin: 4px 0 0; color: #475569; font-weight: 500;">{{ $order->pengguna?->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Nomor HP / Telepon</span>
                        <p style="margin: 4px 0 0; font-family: monospace; color: #475569; font-weight: 600;">{{ $order->pengguna?->no_telepon ?? '-' }}</p>
                    </div>
                    <div style="border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 4px;">
                        <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Alamat Pengiriman</span>
                        <p style="margin: 6px 0 0; color: #334155; line-height: 1.5; font-weight: 600; font-size: 13px; background: #F8FAFC; padding: 12px; border-radius: 10px; border: 1px solid #F1F5F9;">{{ $order->alamat?->alamat_lengkap ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Info Panel --}}
            @if($order->pembayaran)
                <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.05); background: white;">
                    <div class="panel-header" style="padding: 18px 24px; border-bottom: 1px solid #F1F5F9;">
                        <p class="panel-title" style="font-size: 14px; font-weight: 800; color: #1E293B;">Informasi Pembayaran</p>
                    </div>
                    <div class="panel-body" style="padding: 20px; font-size: 13.5px; display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Metode Pembayaran</span>
                            <p style="margin: 4px 0 0; font-weight: 750; color: #1E293B;">{{ $order->pembayaran?->metode?->nama_metode ?? '-' }}</p>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Tanggal Pembayaran</span>
                            <p style="margin: 4px 0 0; color: #475569; font-weight: 500;">
                                {{ $order->pembayaran?->tanggal_pembayaran ? $order->pembayaran->tanggal_pembayaran->format('d/m/Y H:i') : 'Belum Terbayar' }}
                            </p>
                        </div>
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Bukti Transfer</span>
                            <div style="margin-top: 8px;">
                                @if($order->pembayaran?->bukti_pembayaran)
                                    <button type="button"
                                            @click="proofUrl='{{ Storage::url($order->pembayaran->bukti_pembayaran) }}'; proofTitle='{{ $order->kode_transaksi }}'; showProofModal = true"
                                            class="btn-secondary"
                                            style="padding: 8px 12px; font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; width: 100%; justify-content: center; border-radius: 10px;">
                                        📷 Lihat Bukti Transfer
                                    </button>
                                @else
                                    <p style="margin: 0; color: #94A3B8; font-style: italic; font-weight: 500;">Tidak ada lampiran bukti</p>
                                @endif
                            </div>
                        </div>

                        @if($order->pembayaran && $order->pembayaran->status_pembayaran === 'menunggu_konfirmasi')
                            <div style="border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 4px; display: flex; flex-direction: column; gap: 8px;">
                                <form method="POST" action="{{ route('admin.customer-order.verify-payment', $order->pembayaran->pembayaran_id) }}" style="margin: 0; width: 100%;">
                                    @csrf
                                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; background: #10B981; border-radius: 10px; padding: 10px; font-size: 13px; font-weight: 700; box-shadow: 0 4px 12px rgba(16,185,129,0.25); border: none; cursor: pointer;">
                                        ✓ Verifikasi Pembayaran
                                    </button>
                                </form>
                                <button type="button" @click="showRejectModal = true" class="btn-danger" style="width: 100%; justify-content: center; border-radius: 10px; padding: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; color: white;">
                                    ✕ Reject Pembayaran
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- MODAL BUKTI PEMBAYARAN --}}
    <div x-show="showProofModal" x-cloak
         @click.self="showProofModal = false"
         style="position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(8px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 24px; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3); width: 100%; max-width: 650px; overflow: hidden; border: 1px solid #E2E8F0;"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translateY(10px)"
             x-transition:enter-end="opacity-100 scale-100 translateY(0)">
            
            <div style="background: #63A2BB; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06);">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; color: white; margin: 0;">Bukti Pembayaran</h3>
                    <p style="font-size: 11px; color: rgba(255,255,255,0.85); margin: 4px 0 0; font-family: monospace;" x-text="proofTitle"></p>
                </div>
                <button type="button" @click="showProofModal = false" style="background: rgba(255,255,255,0.2); border: none; cursor: pointer; color: white; height: 28px; width: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
            </div>
            
            <div style="background: #F8FAFC; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 280px; max-height: 75vh; overflow-y: auto;">
                <div style="background: white; border: 1.5px solid #E2E8F0; border-radius: 16px; padding: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); max-width: 100%;">
                    <img :src="proofUrl" alt="Bukti Pembayaran" style="max-height: 60vh; max-width: 100%; object-fit: contain; border-radius: 10px;">
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL REJECT PEMBAYARAN --}}
    <div x-show="showRejectModal" x-cloak
         @click.self="showRejectModal = false"
         style="position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(8px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 24px; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3); width: 100%; max-width: 500px; overflow: hidden; border: 1px solid #E2E8F0;"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translateY(10px)"
             x-transition:enter-end="opacity-100 scale-100 translateY(0)">
            
            <div style="background: #EF4444; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); color: white;">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; margin: 0; color: white;">Reject Pembayaran</h3>
                    <p style="font-size: 11px; margin: 4px 0 0; font-family: monospace; color: white;">{{ $order->kode_transaksi }}</p>
                </div>
                <button type="button" @click="showRejectModal = false" style="background: rgba(255,255,255,0.2); border: none; cursor: pointer; color: white; height: 28px; width: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
            </div>
            
            <form method="POST" action="{{ route('admin.customer-order.reject', $order->transaksi_id) }}" style="padding: 24px; margin: 0; display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div>
                    <label for="reject_reason" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Alasan Penolakan</label>
                    <input type="text" id="reject_reason" name="alasan_reject" required placeholder="Masukkan alasan penolakan..." class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 16px; width: 100%; box-sizing: border-box; border: 1.5px solid #E2E8F0; background: #F8FAFC;">
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 8px;">
                    <button type="button" @click="showRejectModal = false" class="btn-secondary" style="height: 38px; padding: 0 16px; border-radius: 10px; font-weight: 600; cursor: pointer; border: 1.5px solid #E2E8F0; background: white;">Batal</button>
                    <button type="submit" id="submitRejectBtn" class="btn-danger" style="height: 38px; padding: 0 20px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; color: white; display: inline-flex; align-items: center; gap: 6px;">Reject Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

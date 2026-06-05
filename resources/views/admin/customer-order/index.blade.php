@extends('layouts.admin')

@section('title', 'Customer Order Management')

@section('content')
@php
    $paymentStatusMap = [
        'menunggu' => ['label' => 'Belum Diverifikasi', 'class' => 'badge-warning', 'can_verify' => true],
        'menunggu_konfirmasi' => ['label' => 'Belum Diverifikasi', 'class' => 'badge-warning', 'can_verify' => true],
        'berhasil' => ['label' => 'Berhasil', 'class' => 'badge-success', 'can_verify' => false],
        'gagal' => ['label' => 'Ditolak', 'class' => 'badge-danger', 'can_verify' => false],
        'expired' => ['label' => 'Expired', 'class' => 'badge-danger', 'can_verify' => false],
        'refund' => ['label' => 'Refund', 'class' => 'badge-info', 'can_verify' => false],
    ];
@endphp

<div style="padding: 32px; max-width: 1600px; margin: 0 auto;" x-data="{
        modalBukti: false,
        currentOrderId: null,
        currentKode: '',
        currentBuktiUrl: '',
        canVerify: false,
        savingPaymentAction: false,

        getStatusBadgeHtml(status) {
            if (status === 'berhasil') {
                return '<span class=\'badge badge-success\' style=\'font-size: 11px; padding: 4px 10px;\'>Berhasil</span>';
            }
            return '<span class=\'badge badge-danger\' style=\'font-size: 11px; padding: 4px 10px;\'>Ditolak</span>';
        },

        getActionBadgeHtml(status) {
            if (status === 'berhasil') {
                return '<span class=\'badge badge-success\' style=\'padding: 6px 12px; font-weight:700; font-size: 11px;\'>Sudah diverifikasi</span>';
            }
            return '<span class=\'badge badge-danger\' style=\'padding: 6px 12px; font-weight:700; font-size: 11px;\'>Ditolak</span>';
        },

        updateRowAfterAction(orderId, status) {
            const statusEl = document.getElementById('status-bayar-' + orderId);
            if (statusEl) {
                statusEl.outerHTML = this.getStatusBadgeHtml(status);
            }

            const actionEl = document.getElementById('payment-action-' + orderId);
            if (actionEl) {
                actionEl.innerHTML = this.getActionBadgeHtml(status);
            }
        },

        async updatePaymentStatus(orderId, status) {
            this.savingPaymentAction = true;
            try {
                const res = await fetch('/admin/customer-order/' + orderId + '/' + (status === 'berhasil' ? 'verify' : 'reject'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const data = await res.json();
                if (data.success) {
                    this.modalBukti = false;
                    this.updateRowAfterAction(orderId, status);

                    showAdminToast(status === 'berhasil'
                        ? '✅ Pembayaran diverifikasi & pesanan diproses'
                        : '❌ Pembayaran ditolak');
                } else {
                    alert(data.message ?? 'Gagal memproses pembayaran');
                }
            } catch (e) {
                alert('Terjadi kesalahan: ' + e.message);
            } finally {
                this.savingPaymentAction = false;
            }
        }
    }">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Transaksi</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Customer Order Management</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Kelola pesanan, verifikasi pembayaran, dan track pengiriman.</p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="panel" style="margin-bottom: 28px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);">
        <div class="panel-body" style="padding: 24px;">
            <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 280px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Pencarian</label>
                    <input type="text" name="search" value="{{ $search_filter ?? '' }}" placeholder="Cari No Pesanan atau Nama Pembeli..." class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 16px; background-color: #F8FAFC;">
                </div>

                <div style="flex: 1; min-width: 180px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Status Order</label>
                    <select name="status" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Status</option>
                        <option value="pembayaran_menunggu" {{ ($status_filter ?? '') === 'pembayaran_menunggu' ? 'selected' : '' }}>Pembayaran Menunggu</option>
                        <option value="pembayaran_dikonfirmasi" {{ ($status_filter ?? '') === 'pembayaran_dikonfirmasi' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                        <option value="pesanan_diproses" {{ ($status_filter ?? '') === 'pesanan_diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="pesanan_dikirim" {{ ($status_filter ?? '') === 'pesanan_dikirim' ? 'selected' : '' }}>Dikirim</option>
                        <option value="pesanan_selesai" {{ ($status_filter ?? '') === 'pesanan_selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="pesanan_dibatalkan" {{ ($status_filter ?? '') === 'pesanan_dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
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
                    @if(request('search') || request('status') || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.customer-order.index') }}" class="btn-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 10px; font-weight: 600; color: #64748B; border: 1.5px solid #E2E8F0; background: white;">Reset</a>
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
                        <th style="width: 160px; padding: 16px 20px;">No Pesanan</th>
                        <th style="padding: 16px 20px;">Pembeli</th>
                        <th style="width: 130px; padding: 16px 20px;">Tanggal</th>
                        <th style="text-align: right; width: 140px; padding: 16px 20px;">Total</th>
                        <th style="text-align: center; width: 180px; padding: 16px 20px;">Status Pembayaran</th>
                        <th style="text-align: center; width: 120px; padding: 16px 20px;">Bukti Bayar</th>
                        <th style="text-align: center; width: 200px; padding: 16px 20px;">Aksi / Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $paymentStatusRaw = $order->pembayaran?->status_pembayaran ?? 'menunggu';
                            $paymentKey = $paymentStatusRaw === 'menunggu_konfirmasi' ? 'menunggu' : $paymentStatusRaw;
                            $paymentInfo = $paymentStatusMap[$paymentKey] ?? $paymentStatusMap['menunggu'];

                            $orderStatusRaw = $order->status ?? '';
                            $canVerify = in_array($paymentStatusRaw, ['menunggu', 'menunggu_konfirmasi'], true) && ! empty($order->pembayaran?->bukti_pembayaran);
                        @endphp
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 13.5px; padding: 16px 20px;">{{ $order->kode_transaksi }}</td>
                            <td style="padding: 16px 20px;">
                                <div style="font-weight: 700; color: #334155; font-size: 14px;">{{ $order->pengguna?->nama_pengguna ?? '-' }}</div>
                                <div style="font-size: 11px; color: #94A3B8; font-weight: 600; margin-top: 2px;">{{ $order->pengguna?->email ?? '-' }}</div>
                            </td>
                            <td style="color: #64748B; font-size: 13px; padding: 16px 20px;">{{ !empty($order->tanggal) ? \Carbon\Carbon::parse($order->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: right; font-weight: 850; color: #0F172A; font-family: monospace; padding: 16px 20px; font-size: 14px;">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align: center; padding: 16px 20px;">
                                <span id="status-bayar-{{ $order->transaksi_id }}" class="badge {{ $paymentInfo['class'] }}" style="font-size: 11px; padding: 4px 10px;">{{ $paymentInfo['label'] }}</span>
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                @if($order->pembayaran?->bukti_pembayaran)
                                    <button type="button"
                                            @click="modalBukti = true; currentBuktiUrl = @js(Storage::url($order->pembayaran->bukti_pembayaran)); currentOrderId = {{ $order->transaksi_id }}; currentKode = @js($order->kode_transaksi); canVerify = @js($canVerify);"
                                            style="border: 2px solid rgba(99,162,187,0.2); border-radius: 8px; overflow: hidden; padding: 0; cursor: pointer; background: none; display: inline-flex; transition: all 0.15s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.03);"
                                            onmouseover="this.style.borderColor='#63A2BB'; this.style.transform='scale(1.03)';"
                                            onmouseout="this.style.borderColor='rgba(99,162,187,0.2)'; this.style.transform='scale(1)';">
                                        <img src="{{ Storage::url($order->pembayaran->bukti_pembayaran) }}"
                                             alt="Bukti"
                                             style="height: 32px; width: 48px; object-fit: cover;">
                                    </button>
                                @else
                                    <span style="font-size: 11px; font-style: italic; color: #CBD5E1; font-weight: 500;">Belum upload</span>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <a href="{{ route('admin.customer-order.show', $order->kode_transaksi) }}"
                                       class="btn-secondary"
                                       style="padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;"
                                       title="Detail">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>

                                    <div id="payment-action-{{ $order->transaksi_id }}" style="display: inline-flex; gap: 6px;">
                                        @if($paymentInfo['can_verify'] && ! empty($order->pembayaran?->bukti_pembayaran))
                                            <button type="button"
                                                    @click="updatePaymentStatus({{ $order->transaksi_id }}, 'berhasil')"
                                                    :disabled="savingPaymentAction"
                                                    style="display: inline-flex; align-items: center; justify-content: center; height: 32px; width: 32px; background: #ECFDF5; border: 1.5px solid #A7F3D0; color: #059669; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;"
                                                    onmouseover="this.style.background='#D1FAE5'; this.style.borderColor='#34D399';"
                                                    onmouseout="this.style.background='#ECFDF5'; this.style.borderColor='#A7F3D0';"
                                                    title="Setujui pembayaran">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </button>

                                            <button type="button"
                                                    @click="updatePaymentStatus({{ $order->transaksi_id }}, 'gagal')"
                                                    :disabled="savingPaymentAction"
                                                    style="display: inline-flex; align-items: center; justify-content: center; height: 32px; width: 32px; background: #FFF5F5; border: 1.5px solid #FED7D7; color: #E53E3E; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;"
                                                    onmouseover="this.style.background='#FED7D7'; this.style.borderColor='#F87171';"
                                                    onmouseout="this.style.background='#FFF5F5'; this.style.borderColor='#FED7D7';"
                                                    title="Tolak pembayaran">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @else
                                            <span class="badge" style="background:#F1F5F9; color:#64748B; font-size: 11px; padding: 4px 10px;">
                                                {{ in_array($paymentStatusRaw, ['gagal', 'ditolak', 'expired'], true) ? 'Selesai' : 'Diverifikasi' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94A3B8; padding: 48px; font-size: 14px; font-weight: 500;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Tidak ada data pesanan customer yang cocok.</span>
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

    {{-- MODAL BUKTI PEMBAYARAN (CUSTOM DARK GLASSMORPHISM) --}}
    <div x-show="modalBukti" x-cloak style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;" @click.self="modalBukti = false">
        <div style="background: white; border-radius: 24px; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3); width: 100%; max-width: 500px; overflow: hidden; border: 1px solid rgba(255,255,255,0.8);"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translateY(10px)"
             x-transition:enter-end="opacity-100 scale-100 translateY(0)">
            
            <div style="background: #0F172A; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06);">
                <div>
                    <h3 style="font-size: 15px; font-weight: 800; color: white; margin: 0;">Bukti Pembayaran</h3>
                    <p style="font-size: 11px; color: #94A3B8; margin: 4px 0 0; font-family: monospace; font-weight: 600;" x-text="currentKode"></p>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a :href="currentBuktiUrl" target="_blank" style="font-size: 11px; font-weight: 700; color: #E2E8F0; text-decoration: none; padding: 6px 12px; background: rgba(255,255,255,0.1); border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.18)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">🔗 Buka Asli</a>
                    <button @click="modalBukti = false" style="background: rgba(255,255,255,0.15); border: none; cursor: pointer; color: white; height: 28px; width: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">✕</button>
                </div>
            </div>

            <div style="background: #0F172A; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 280px; max-height: 400px; overflow: hidden; border-bottom: 1px solid rgba(255,255,255,0.06);">
                <img :src="currentBuktiUrl" alt="Bukti Pembayaran" style="max-height: 360px; max-width: 100%; object-fit: contain; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.4);">
            </div>

            <div style="padding: 18px 24px; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC;">
                <span style="font-size: 12px; color: #64748B; font-weight: 500;">Detail bukti transfer customer</span>
                
                <button @click="updatePaymentStatus(currentOrderId, 'berhasil')" :disabled="savingPaymentAction || !canVerify" class="btn-primary" style="padding: 10px 20px; border-radius: 10px; display: flex; align-items: center; gap: 8px; background: #10B981; border: none; font-size: 13px; font-weight: 700; box-shadow: 0 4px 12px rgba(16,185,129,0.25); cursor: pointer;">
                    <svg x-show="savingPaymentAction" class="animate-spin" width="12" height="12" viewBox="0 0 24 24" fill="none" style="margin-right: 2px;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <svg x-show="!savingPaymentAction" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="savingPaymentAction ? 'Memproses...' : 'Verifikasi Pembayaran'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

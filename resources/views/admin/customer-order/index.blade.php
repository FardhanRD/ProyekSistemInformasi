@extends('layouts.admin')

@section('title', 'Customer Order Management')

@section('content')
@php
    $paymentStatusMap = [
        'menunggu' => ['label' => 'Belum Diverifikasi', 'class' => 'bg-amber-50 text-amber-600', 'can_verify' => true],
        'menunggu_konfirmasi' => ['label' => 'Belum Diverifikasi', 'class' => 'bg-amber-50 text-amber-600', 'can_verify' => true],
        'berhasil' => ['label' => 'Berhasil', 'class' => 'bg-green-50 text-green-600', 'can_verify' => false],
        'gagal' => ['label' => 'Ditolak', 'class' => 'bg-red-50 text-red-500', 'can_verify' => false],
        'expired' => ['label' => 'Expired', 'class' => 'bg-gray-50 text-gray-500', 'can_verify' => false],
        'refund' => ['label' => 'Refund', 'class' => 'bg-gray-50 text-gray-500', 'can_verify' => false],
    ];
@endphp

<div x-data="{
        modalBukti: false,
        currentOrderId: null,
        currentKode: '',
        currentBuktiUrl: '',
        canVerify: false,
        savingPaymentAction: false,

        getStatusBadgeHtml(status) {
            if (status === 'berhasil') {
                return '<span class=\'text-xs font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-600\'>Berhasil</span>';
            }

            return '<span class=\'text-xs font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-500\'>Ditolak</span>';
        },

        getActionBadgeHtml(status) {
            if (status === 'berhasil') {
                return '<span class=\'inline-flex items-center rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-600\'>Sudah diverifikasi</span>';
            }

            return '<span class=\'inline-flex items-center rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500\'>Ditolak</span>';
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
    }" class="space-y-6">
    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Customer Order Management</h1>
                <p class="text-slate-600">Kelola pesanan, verifikasi pembayaran, dan track pengiriman.</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
            <input type="text" name="search" value="{{ $search_filter ?? '' }}" placeholder="Cari No Pesanan atau Nama Pembeli..." class="min-w-[200px] flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pembayaran_menunggu" {{ ($status_filter ?? '') === 'pembayaran_menunggu' ? 'selected' : '' }}>Pembayaran Menunggu</option>
                <option value="pembayaran_dikonfirmasi" {{ ($status_filter ?? '') === 'pembayaran_dikonfirmasi' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                <option value="pesanan_diproses" {{ ($status_filter ?? '') === 'pesanan_diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="pesanan_dikirim" {{ ($status_filter ?? '') === 'pesanan_dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="pesanan_selesai" {{ ($status_filter ?? '') === 'pesanan_selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="pesanan_dibatalkan" {{ ($status_filter ?? '') === 'pesanan_dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <input type="date" name="start_date" value="{{ $start_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">
            <input type="date" name="end_date" value="{{ $end_date ?? '' }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">

            <button type="submit" class="rounded-xl bg-[#63A2BB] px-5 py-2 text-sm font-semibold text-white hover:bg-[#4f93ac]">Filter</button>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-slate-700">
                        <th class="px-4 py-3">No Pesanan</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status Pembayaran</th>
                        <th class="px-4 py-3">Bukti Bayar</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $paymentStatusRaw = $order->pembayaran?->status_pembayaran ?? 'menunggu';
                            $paymentKey = $paymentStatusRaw === 'menunggu_konfirmasi' ? 'menunggu' : $paymentStatusRaw;
                            $paymentInfo = $paymentStatusMap[$paymentKey] ?? $paymentStatusMap['menunggu'];

                            $orderStatusRaw = $order->status ?? '';
                            $orderInfo = $orderStatusMap[$orderStatusRaw] ?? [
                                'label' => $orderStatusRaw ? ucfirst(str_replace('_', ' ', $orderStatusRaw)) : '-',
                                'class' => 'bg-gray-50 text-gray-500',
                                'value' => $orderStatusRaw,
                            ];

                            $canVerify = in_array($paymentStatusRaw, ['menunggu', 'menunggu_konfirmasi'], true) && ! empty($order->pembayaran?->bukti_pembayaran);
                        @endphp
                        <tr class="border-t border-slate-100 text-xs hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono font-medium">{{ $order->kode_transaksi }}</td>
                            <td class="px-4 py-3">{{ $order->pengguna?->nama_pengguna ?? '-' }}</td>
                            <td class="px-4 py-3">{{ !empty($order->tanggal) ? \Carbon\Carbon::parse($order->tanggal)->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span id="status-bayar-{{ $order->transaksi_id }}" class="text-xs font-bold px-2.5 py-1 rounded-full {{ $paymentInfo['class'] }}">{{ $paymentInfo['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($order->pembayaran?->bukti_pembayaran)
                                    <button type="button"
                                            @click="modalBukti = true; currentBuktiUrl = @js(Storage::url($order->pembayaran->bukti_pembayaran)); currentOrderId = {{ $order->transaksi_id }}; currentKode = @js($order->kode_transaksi); canVerify = @js($canVerify);"
                                            class="group relative block overflow-hidden rounded-xl border-2 border-[#63A2BB]/30 transition hover:border-[#63A2BB]">
                                        <img src="{{ Storage::url($order->pembayaran->bukti_pembayaran) }}"
                                             alt="Bukti pembayaran"
                                             class="h-10 w-14 object-cover transition-transform group-hover:scale-110">
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 transition group-hover:opacity-100">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                            </svg>
                                        </div>
                                    </button>
                                @else
                                    <span class="text-xs italic text-gray-300">Belum upload</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.customer-order.show', $order->kode_transaksi) }}"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 transition hover:bg-[#63A2BB]/10 hover:text-[#63A2BB]"
                                       title="Detail">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @if($paymentInfo['can_verify'] && ! empty($order->pembayaran?->bukti_pembayaran))
                                        <div class="flex items-center gap-2" id="payment-action-{{ $order->transaksi_id }}">
                                            <button type="button"
                                                    @click="updatePaymentStatus({{ $order->transaksi_id }}, 'berhasil')"
                                                    :disabled="savingPaymentAction"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-60"
                                                    title="Setujui pembayaran"
                                                    aria-label="Setujui pembayaran">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>

                                            <button type="button"
                                                    @click="updatePaymentStatus({{ $order->transaksi_id }}, 'gagal')"
                                                    :disabled="savingPaymentAction"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-60"
                                                    title="Tolak pembayaran"
                                                    aria-label="Tolak pembayaran">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">{{ in_array($paymentStatusRaw, ['gagal', 'ditolak'], true) ? 'Ditolak' : ($paymentInfo['can_verify'] ? 'Menunggu bukti' : 'Sudah diverifikasi') }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-600">Tidak ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders ?? null, 'links'))
            <div class="border-t border-slate-100 p-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL BUKTI PEMBAYARAN --}}
    <div x-show="modalBukti" x-cloak @click.self="modalBukti = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between bg-gray-800 px-5 py-3">
                <div>
                    <h3 class="text-sm font-bold text-white">Bukti Pembayaran</h3>
                    <p class="mt-0.5 text-xs text-gray-400" x-text="currentKode"></p>
                </div>
                <div class="flex gap-2">
                    <a :href="currentBuktiUrl" target="_blank" class="rounded-lg bg-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-300 transition hover:bg-gray-600">🔗 Buka Asli</a>
                    <button @click="modalBukti = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-700 text-white transition hover:bg-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex min-h-48 max-h-96 items-center justify-center overflow-hidden bg-gray-900 p-4">
                <img :src="currentBuktiUrl" alt="Bukti Pembayaran" class="max-h-80 max-w-full rounded-lg object-contain shadow-xl">
            </div>

            <div class="flex items-center justify-between border-t border-gray-100 p-4">
                <div class="text-sm text-gray-500">Klik gambar untuk zoom, atau buka asli di tab baru</div>
                <button @click="submitVerifikasi()" :disabled="savingPaymentAction || !canVerify" class="flex items-center gap-2 rounded-xl bg-green-500 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-green-600 disabled:opacity-60">
                    <svg x-show="savingPaymentAction" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="!savingPaymentAction" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="savingPaymentAction ? 'Memverifikasi...' : '✓ Verifikasi Pembayaran'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

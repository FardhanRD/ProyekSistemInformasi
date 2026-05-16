@extends('layouts.app')

@section('title','Tracking Pesanan')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Header Section -->
            <div class="mb-4">
                <h2>Riwayat Pengiriman</h2>
                <p class="text-muted">Kode Transaksi: <strong>{{ $transaksi->kode_transaksi }}</strong></p>
            </div>

            <!-- Order Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><small class="text-muted">STATUS PESANAN</small></p>
                            <h6 class="mb-0">
                                @if($transaksi->status === 'menunggu_pembayaran')
                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                @elseif($transaksi->status === 'dibayar' || $transaksi->status === 'pembayaran_dikonfirmasi')
                                    <span class="badge bg-info">Dibayar</span>
                                @elseif($transaksi->status === 'diproses')
                                    <span class="badge bg-primary">Diproses</span>
                                @elseif($transaksi->status === 'dikirim')
                                    <span class="badge bg-info">Dikirim</span>
                                @elseif($transaksi->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($transaksi->status === 'batal')
                                    <span class="badge bg-danger">Batal</span>
                                @else
                                    <span class="badge bg-secondary">{{ $transaksi->status }}</span>
                                @endif
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><small class="text-muted">TOTAL PESANAN</small></p>
                            <h6 class="mb-0">Rp {{ number_format($transaksi->total_harga ?? 0, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><small class="text-muted">TUJUAN PENGIRIMAN</small></p>
                            <p class="mb-0">
                                <strong>{{ $alamatTujuan->nama_penerima ?? '-' }}</strong><br>
                                <small>{{ $alamatTujuan->alamat_lengkap ?? '-' }}<br>
                                {{ $alamatTujuan->kota ?? '-' }}, {{ $alamatTujuan->provinsi ?? '-' }}</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><small class="text-muted">JASA PENGIRIMAN</small></p>
                            <p class="mb-0">
                                <strong>{{ $ekspedisi->nama_ekspedisi ?? 'Reguler' }}</strong><br>
                                @if($pesanan)
                                    <small>Resi: <strong>{{ $pesanan->no_resi ?? '-' }}</strong></small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Status Pengiriman</h5>
                </div>
                <div class="card-body">
                    @if(!$trackingLogs->isEmpty())
                        <div class="timeline">
                            @foreach($trackingLogs as $index => $log)
                                <div class="timeline-item {{ $index === $trackingLogs->count() - 1 ? 'active' : '' }}">
                                    <div class="timeline-marker">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $log->status }}</h6>
                                        <p class="mb-1 small text-muted">
                                            {{ \Carbon\Carbon::parse($log->waktu_update)->format('d M Y H:i') }}
                                        </p>
                                        <p class="mb-0 small">{{ $log->deskripsi }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <style>
                            .timeline {
                                position: relative;
                                padding-left: 30px;
                            }

                            .timeline::before {
                                content: '';
                                position: absolute;
                                left: 7px;
                                top: 0;
                                bottom: 0;
                                width: 2px;
                                background-color: #dee2e6;
                            }

                            .timeline-item {
                                position: relative;
                                margin-bottom: 25px;
                            }

                            .timeline-item.active .timeline-marker {
                                background-color: #198754;
                                color: white;
                            }

                            .timeline-marker {
                                position: absolute;
                                left: -23px;
                                top: 0;
                                width: 30px;
                                height: 30px;
                                background-color: #e9ecef;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 14px;
                                color: #495057;
                            }

                            .timeline-content {
                                padding: 10px;
                                background-color: #f8f9fa;
                                border-radius: 4px;
                                border-left: 3px solid #dee2e6;
                            }

                            .timeline-item.active .timeline-content {
                                background-color: #f0f9ff;
                                border-left-color: #198754;
                            }
                        </style>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i>
                            @if(!$pesanan)
                                Pesanan belum diproses untuk pengiriman. Silakan tunggu konfirmasi dari penjual.
                            @else
                                Belum ada update tracking. Pesanan sedang disiapkan untuk dikirim.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <a href="{{ route('order.show', $transaksi->transaksi_id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail Pesanan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

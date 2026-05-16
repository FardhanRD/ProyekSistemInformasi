<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->kode_transaksi }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company { font-size: 24px; font-weight: bold; }
        .info-section { margin-bottom: 30px; }
        .label { font-weight: bold; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">MOVR</div>
        <div>Invoice Pesanan Pelanggan</div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="info-section">
            <div><span class="label">No Pesanan:</span> {{ $order->kode_transaksi }}</div>
            <div><span class="label">Tanggal:</span> {{ $order->tanggal?->format('Y-m-d H:i') ?? '-' }}</div>
            <div><span class="label">Status:</span> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
        </div>
        <div class="info-section">
            <div><span class="label">Pembeli:</span> {{ $order->pengguna?->nama_pengguna ?? '-' }}</div>
            <div><span class="label">Email:</span> {{ $order->pengguna?->email ?? '-' }}</div>
            <div><span class="label">Telepon:</span> {{ $order->pengguna?->no_telepon ?? '-' }}</div>
        </div>
    </div>

    <div class="info-section">
        <div><span class="label">Alamat Pengiriman:</span></div>
        <div>{{ $order->alamat?->alamat_lengkap ?? '-' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: right;">Qty</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->details ?? [] as $item)
                <tr>
                    <td>
                        {{ $item->produk?->nama_produk ?? '-' }}<br>
                        <small>{{ $item->warna?->nama_warna ?? '-' }}</small>
                    </td>
                    <td style="text-align: right;">{{ $item->qty }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format(($item->qty ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada item</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Invoice ini dicetak dari sistem MOVR pada {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Terima kasih telah berbelanja!</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice PO {{ $po->kode_order }}</title>
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
        <div>Purchase Order (PO)</div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="info-section">
            <div><span class="label">Kode PO:</span> {{ $po->kode_order }}</div>
            <div><span class="label">Tanggal:</span> {{ $po->tanggal_order?->format('Y-m-d H:i') ?? '-' }}</div>
            <div><span class="label">Status:</span> {{ ucfirst($po->status) }}</div>
        </div>
        <div class="info-section">
            <div><span class="label">Supplier:</span> {{ $po->supplier?->nama_toko ?? '-' }}</div>
            <div><span class="label">Pemilik:</span> {{ $po->supplier?->pemilik ?? '-' }}</div>
            <div><span class="label">Telepon:</span> {{ $po->supplier?->no_telepon ?? '-' }}</div>
        </div>
    </div>

    @if($po->catatan)
        <div class="info-section">
            <div><span class="label">Catatan:</span></div>
            <div>{{ $po->catatan }}</div>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: right;">Qty</th>
                <th style="text-align: right;">Harga Beli</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($po->details ?? [] as $detail)
                <tr>
                    <td>
                        {{ $detail->detailProduk?->produk?->nama_produk ?? '-' }}<br>
                        <small>{{ $detail->detailProduk?->warna?->nama_warna ?? '-' }}</small>
                    </td>
                    <td style="text-align: right;">{{ $detail->qty }}</td>
                    <td style="text-align: right;">Rp {{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada item</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>PO ini dicetak dari sistem MOVR pada {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Kirim barang sesuai dengan detail di atas</p>
    </div>
</body>
</html>

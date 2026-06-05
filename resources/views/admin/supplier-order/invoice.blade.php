<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice PO — {{ $po->kode_order }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { print-color-adjust: exact; }
    }

    body {
      font-family: Arial, sans-serif;
    }
  </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8">
  <div class="no-print mx-auto mb-6 flex max-w-4xl gap-3">
    <button onclick="window.print()" class="flex items-center gap-2 rounded-xl bg-[#63A2BB] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#4A8BA3]">
      🖨️ Print / Simpan PDF
    </button>
    <a href="javascript:history.back()" class="rounded-xl border-2 border-gray-200 px-5 py-2.5 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
      ← Kembali
    </a>
  </div>

  <div class="mx-auto max-w-4xl overflow-hidden rounded-2xl bg-white shadow-lg">
    <div class="flex items-center justify-between bg-gray-900 px-6 py-6 sm:px-8">
      <div class="flex items-center gap-4">
        <img src="{{ asset('images/logo-movr.png') }}" alt="MOVR" class="h-10 w-auto brightness-0 invert" onerror="this.style.display='none'">
        <div class="text-white">
          <p class="text-xl font-black tracking-wider">MOVR</p>
          <p class="text-xs text-gray-400">Purchase Order</p>
        </div>
      </div>
      <div class="text-right">
        <p class="text-2xl font-black text-white">{{ $po->kode_order }}</p>
        <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-bold text-white {{ $po->status === 'diterima' ? 'bg-green-500' : ($po->status === 'draft' ? 'bg-slate-500' : 'bg-amber-500') }}">
          {{ strtoupper($po->status) }}
        </span>
      </div>
    </div>

    <div class="px-6 py-6 sm:px-8">
      <div class="mb-8 grid grid-cols-1 gap-8 sm:grid-cols-2">
        <div>
          <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Dari (Pembeli)</p>
          <div class="space-y-1">
            <p class="font-bold text-gray-800">MOVR Indonesia</p>
            <p class="text-sm text-gray-500">Platform E-Commerce Sportswear</p>
            <p class="text-sm text-gray-500">admin@movr.id</p>
          </div>
        </div>

        <div>
          <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Kepada (Supplier)</p>
          <div class="space-y-1">
            <p class="font-bold text-gray-800">{{ $po->supplier->nama_toko ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $po->supplier->email ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $po->supplier->no_telepon ?? '-' }}</p>
          </div>
        </div>
      </div>

      <div class="mb-8 grid grid-cols-1 gap-4 rounded-xl bg-gray-50 p-4 sm:grid-cols-3">
        <div>
          <p class="mb-1 text-xs text-gray-400">Tanggal PO</p>
          <p class="text-sm font-semibold text-gray-700">{{ optional($po->tanggal_order)->isoFormat('D MMMM YYYY') ?? '-' }}</p>
        </div>
        <div>
          <p class="mb-1 text-xs text-gray-400">Estimasi Terima</p>
          <p class="text-sm font-semibold text-gray-700">{{ $po->tanggal_diterima ? $po->tanggal_diterima->isoFormat('D MMMM YYYY') : '-' }}</p>
        </div>
        <div>
          <p class="mb-1 text-xs text-gray-400">Dibuat Oleh</p>
          <p class="text-sm font-semibold text-gray-700">{{ $po->admin->pengguna->nama_pengguna ?? 'Admin MOVR' }}</p>
        </div>
      </div>

      <div class="mb-8">
        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Detail Produk</p>
        <div class="overflow-x-auto">
          <table class="w-full border-collapse text-sm">
            <thead>
              <tr class="bg-gray-900 text-white">
                <th class="rounded-tl-xl px-4 py-3 text-left text-xs font-bold">No</th>
                <th class="px-4 py-3 text-left text-xs font-bold">Produk</th>
                <th class="px-4 py-3 text-left text-xs font-bold">SKU</th>
                <th class="px-4 py-3 text-center text-xs font-bold">Qty</th>
                <th class="px-4 py-3 text-right text-xs font-bold">Harga Satuan</th>
                <th class="rounded-tr-xl px-4 py-3 text-right text-xs font-bold">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($po->details as $i => $item)
                <tr class="border-b border-gray-100 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                  <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                  <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">{{ $item->detailProduk->produk->nama_produk ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $item->detailProduk->warna->nama_warna ?? '-' }}{{ $item->detailProduk->ukuran ? ' · ' . $item->detailProduk->ukuran : '' }}</p>
                  </td>
                  <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->detailProduk->sku ?? '-' }}</td>
                  <td class="px-4 py-3 text-center font-semibold">{{ number_format($item->qty) }}</td>
                  <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                  <td class="px-4 py-3 text-right font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada item.</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr class="bg-gray-900 text-white">
                <td colspan="5" class="rounded-bl-xl px-4 py-3 text-right text-sm font-bold">TOTAL PO</td>
                <td class="rounded-br-xl px-4 py-3 text-right text-lg font-black">Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      @if($po->catatan)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
          <p class="mb-1 text-xs font-bold text-amber-600">Catatan:</p>
          <p class="text-sm text-amber-700">{{ $po->catatan }}</p>
        </div>
      @endif

      <div class="mt-8 grid grid-cols-1 gap-8 border-t border-gray-200 pt-6 sm:grid-cols-2">
        <div class="text-center">
          <p class="mb-16 text-xs text-gray-400">Disetujui oleh</p>
          <div class="border-t-2 border-gray-300 pt-2">
            <p class="text-sm font-semibold text-gray-700">{{ $po->admin->pengguna->nama_pengguna ?? 'Admin MOVR' }}</p>
            <p class="text-xs text-gray-400">Admin MOVR</p>
          </div>
        </div>
        <div class="text-center">
          <p class="mb-16 text-xs text-gray-400">Diterima oleh</p>
          <div class="border-t-2 border-gray-300 pt-2">
            <p class="text-sm font-semibold text-gray-700">{{ $po->supplier->nama_toko ?? '-' }}</p>
            <p class="text-xs text-gray-400">Supplier</p>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-200 bg-gray-50 px-8 py-4 text-center">
      <p class="text-xs text-gray-400">Dokumen ini digenerate otomatis oleh sistem MOVR · {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
    </div>
  </div>
</body>
</html>

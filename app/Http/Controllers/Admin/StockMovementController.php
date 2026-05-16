<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('stock_movement')) {
            return view('admin.stock-movement.index', [
                'movements' => collect(),
                'produk_list' => collect(),
            ]);
        }

        $jenis = $request->get('jenis');
        $produk_id = $request->get('produk_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $movements = StockMovement::with(['detailProduk.produk', 'detailProduk.warna'])
            ->when($jenis, fn($q) => $q->where('jenis', strtolower($jenis)))
            ->when($produk_id, fn($q) => $q->whereHas('detailProduk.produk', fn($sq) => $sq->where('produk_id', $produk_id)))
            ->when($start_date, fn($q) => $q->where('created_at', '>=', $start_date . ' 00:00:00'))
            ->when($end_date, fn($q) => $q->where('created_at', '<=', $end_date . ' 23:59:59'))
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $produk_list = Schema::hasTable('produk')
            ? Produk::where('is_active', 1)->orderBy('nama_produk')->get()
            : collect();

        return view('admin.stock-movement.index', [
            'movements' => $movements,
            'produk_list' => $produk_list,
            'jenis_filter' => $jenis,
            'produk_filter' => $produk_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function export(Request $request)
    {
        if (!Schema::hasTable('stock_movement')) {
            return redirect()->route('admin.stock-movement.index')
                ->with('error', 'Tidak ada data untuk diekspor.');
        }

        $jenis = $request->get('jenis');
        $produk_id = $request->get('produk_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $movements = StockMovement::with(['detailProduk.produk', 'detailProduk.warna'])
            ->when($jenis, fn($q) => $q->where('jenis', strtolower($jenis)))
            ->when($produk_id, fn($q) => $q->whereHas('detailProduk.produk', fn($sq) => $sq->where('produk_id', $produk_id)))
            ->when($start_date, fn($q) => $q->where('created_at', '>=', $start_date . ' 00:00:00'))
            ->when($end_date, fn($q) => $q->where('created_at', '<=', $end_date . ' 23:59:59'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate CSV
        $filename = 'stock-movement-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $csv = fopen('php://memory', 'w');
        fputcsv($csv, ['Tanggal', 'Produk', 'Variant (Warna/Size)', 'SKU', 'Jenis', 'Qty', 'Stok Sebelum', 'Stok Sesudah', 'Referensi', 'Catatan', 'Oleh']);

        foreach ($movements as $m) {
            fputcsv($csv, [
                $m->created_at->format('Y-m-d H:i:s'),
                $m->detailProduk->produk->nama_produk ?? '-',
                ($m->detailProduk->ukuran ?? '-'),
                $m->detailProduk->sku ?? '-',
                $m->jenis,
                $m->qty,
                $m->stok_sebelum,
                $m->stok_sesudah,
                $m->referensi,
                $m->catatan,
                $m->dibuat_oleh,
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content, 200, $headers);
    }
}

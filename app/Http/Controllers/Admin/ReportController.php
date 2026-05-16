<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'revenue');
        $groupBy = $request->get('group_by', 'day');
        $start = $request->get('start', now()->subDays(30)->toDateString());
        $end = $request->get('end', now()->toDateString());
        $kategoriId = $request->get('kategori_id');

        $revenueRows = $this->buildRevenueRows($start, $end, $groupBy);
        $topProducts = $this->buildTopProducts($kategoriId);
        $customerStats = $this->buildCustomerStats($start, $end);
        $stockStats = $this->buildStockStats();

        return view('admin.report.index', [
            'tab' => $tab,
            'groupBy' => $groupBy,
            'start' => $start,
            'end' => $end,
            'kategoriId' => $kategoriId,
            'revenueRows' => $revenueRows,
            'topProducts' => $topProducts,
            'customerStats' => $customerStats,
            'stockStats' => $stockStats,
            'categories' => DB::table('kategori')->orderBy('nama_kategori')->get(),
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $start = $request->get('start', now()->subDays(30)->toDateString());
        $end = $request->get('end', now()->toDateString());
        $groupBy = $request->get('group_by', 'day');

        $revenueRows = $this->buildRevenueRows($start, $end, $groupBy);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.report.export', [
                'revenueRows' => $revenueRows,
                'start' => $start,
                'end' => $end,
                'groupBy' => $groupBy,
            ]);

            return $pdf->download('report-' . $start . '-to-' . $end . '.pdf');
        }

        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Periode', 'Jumlah Order', 'Revenue', 'Avg Order Value']);
        foreach ($revenueRows as $row) {
            fputcsv($csv, [$row['periode'], $row['jumlah_order'], $row['revenue'], $row['avg_order_value']]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-' . $start . '-to-' . $end . '.csv"',
        ]);
    }

    protected function buildRevenueRows(string $start, string $end, string $groupBy): array
    {
        if (!Schema::hasTable('transaksi')) {
            return [];
        }

        $dateExpr = match ($groupBy) {
            'week' => "YEARWEEK(tanggal, 1)",
            'month' => "DATE_FORMAT(tanggal, '%Y-%m')",
            default => "DATE(tanggal)",
        };

        $rows = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->selectRaw("{$dateExpr} as periode, COUNT(*) as jumlah_order, COALESCE(SUM(total_harga),0) as revenue, COALESCE(AVG(total_harga),0) as avg_order_value")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        return $rows->map(function ($row) use ($groupBy) {
            return [
                'periode' => (string) $row->periode,
                'jumlah_order' => (int) $row->jumlah_order,
                'revenue' => (float) $row->revenue,
                'avg_order_value' => (float) $row->avg_order_value,
            ];
        })->toArray();
    }

    protected function buildTopProducts(?string $kategoriId)
    {
        $query = Produk::where('is_active', 1)->orderByDesc('total_terjual')->limit(10);

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        return $query->get();
    }

    protected function buildCustomerStats(string $start, string $end): array
    {
        $newCustomers = Pengguna::where('role', 'buyer')
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as periode, COUNT(*) as total')
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        $topCustomers = Transaksi::with('pengguna')
            ->where('status', 'selesai')
            ->selectRaw('pengguna_id, COUNT(*) as total_order, SUM(total_harga) as total_belanja')
            ->groupBy('pengguna_id')
            ->orderByDesc('total_belanja')
            ->limit(10)
            ->get();

        $buyersWithMoreThanOneOrder = Transaksi::where('status', 'selesai')
            ->select('pengguna_id')
            ->groupBy('pengguna_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $totalBuyers = Pengguna::where('role', 'buyer')->count();

        return [
            'newCustomers' => $newCustomers,
            'topCustomers' => $topCustomers,
            'repeatBuyerRate' => $totalBuyers > 0 ? round(($buyersWithMoreThanOneOrder / $totalBuyers) * 100, 1) : 0,
        ];
    }

    protected function buildStockStats(): array
    {
        $lowStockProducts = DetailProduk::with('produk')
            ->orderBy('stok')
            ->limit(10)
            ->get();

        $inventoryValue = DB::table('detail_produk')
            ->join('produk', 'produk.produk_id', '=', 'detail_produk.produk_id')
            ->join('kategori', 'kategori.kategori_id', '=', 'produk.kategori_id')
            ->selectRaw('kategori.nama_kategori, COALESCE(SUM(detail_produk.stok * detail_produk.harga), 0) as nilai_inventori')
            ->groupBy('kategori.nama_kategori')
            ->orderByDesc('nilai_inventori')
            ->get();

        $stockMovement = StockMovement::selectRaw('DATE(created_at) as periode, SUM(CASE WHEN jenis = "in" THEN qty ELSE 0 END) as total_in, SUM(CASE WHEN jenis = "out" THEN qty ELSE 0 END) as total_out')
            ->groupBy('periode')
            ->orderBy('periode')
            ->limit(30)
            ->get();

        return [
            'lowStockProducts' => $lowStockProducts,
            'inventoryValue' => $inventoryValue,
            'stockMovement' => $stockMovement,
        ];
    }
}

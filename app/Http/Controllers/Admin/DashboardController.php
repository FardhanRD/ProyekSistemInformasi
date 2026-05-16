<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\DetailProduk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengguna;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = $this->parseStart($request);
        $end = $this->parseEnd($request);

        $prevStart = (clone $start)->subMonth();
        $prevEnd = (clone $end)->subMonth();

        // Stat Cards
        $totalRevenue = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('total_harga');

        $prevRevenue = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->sum('total_harga');

        $prevActiveCustomers = Transaksi::whereBetween('tanggal', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->select('pengguna_id')
            ->distinct()
            ->count('pengguna_id');

        $totalOrders = Transaksi::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->count();
        $prevOrders = Transaksi::whereBetween('tanggal', [$prevStart->toDateString(), $prevEnd->toDateString()])->count();

        $activeCustomers = Transaksi::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->select('pengguna_id')
            ->distinct()
            ->count('pengguna_id');

        $prevAvg = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->avg('total_harga');

        $avgOrderValue = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->avg('total_harga');

        // Percent changes
        $revenueChange = $this->pctChange($prevRevenue, $totalRevenue);
        $ordersChange = $this->pctChange($prevOrders, $totalOrders);
        $customersChange = $this->pctChange($prevActiveCustomers, $activeCustomers);
        $avgChange = $this->pctChange($prevAvg, $avgOrderValue);

        // Performance Trends (Revenue by date)
        $trendData = Transaksi::query()
            ->select(DB::raw('DATE(tanggal) as day'), DB::raw('SUM(total_harga) as revenue'))
            ->where('status', 'selesai')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy(DB::raw('DATE(tanggal)'))
            ->get();

        $prevTrendData = Transaksi::query()
            ->select(DB::raw('DATE(tanggal) as day'), DB::raw('SUM(total_harga) as revenue'))
            ->where('status', 'selesai')
            ->whereBetween('tanggal', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy(DB::raw('DATE(tanggal)'))
            ->get();

        // Sales distribution by category (donut)
        // transaksi_detail skema repo: transaksidetail.detail_produk_id, harga_snap, quantity
        $categoryRevenue = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
            ->join('detail_produk', 'transaksi_detail.detail_produk_id', '=', 'detail_produk.detail_produk_id')
            ->join('produk', 'detail_produk.produk_id', '=', 'produk.produk_id')
            ->join('kategori', 'produk.kategori_id', '=', 'kategori.kategori_id')
            ->where('transaksi.status', 'selesai')
            ->whereBetween('transaksi.tanggal', [$start->toDateString(), $end->toDateString()])
            ->select(
                'kategori.nama_kategori as category',
                DB::raw('SUM(COALESCE(transaksi_detail.harga_snap, 0) * COALESCE(transaksi_detail.quantity, 0)) as revenue')
            )
            ->groupBy('kategori.nama_kategori')
            ->orderByDesc('revenue')
            ->get();

        // Monthly revenue comparison (bar): this year vs last year
        $thisYear = $start->year;
        $lastYear = $thisYear - 1;

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[] = [
                'month' => Carbon::create($thisYear, $m, 1)->format('M'),
                'this' => Transaksi::where('status', 'selesai')
                    ->whereYear('tanggal', $thisYear)
                    ->whereMonth('tanggal', $m)
                    ->sum('total_harga'),
                'last' => Transaksi::where('status', 'selesai')
                    ->whereYear('tanggal', $lastYear)
                    ->whereMonth('tanggal', $m)
                    ->sum('total_harga'),
            ];
        }

        // Recent Activities: admin_log (jika ada). Placeholder aman.
        $recentActivities = [];
        if (DB::getSchemaBuilder()->hasTable('admin_log')) {
            $recentActivities = DB::table('admin_log')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('admin.dashboard.index', [
            'start' => $start,
            'end' => $end,
            'totalRevenue' => $totalRevenue,
            'revenueChange' => $revenueChange,
            'totalOrders' => $totalOrders,
            'ordersChange' => $ordersChange,
            'activeCustomers' => $activeCustomers,
            'customersChange' => $customersChange,
            'avgOrderValue' => $avgOrderValue,
            'avgChange' => $avgChange,
            'trendData' => $trendData,
            'prevTrendData' => $prevTrendData,
            'categoryRevenue' => $categoryRevenue,
            'monthly' => $monthly,
            'recentActivities' => $recentActivities,
        ]);
    }

    private function pctChange($prev, $current)
    {
        $prev = is_null($prev) ? 0 : (float) $prev;
        $current = is_null($current) ? 0 : (float) $current;

        if ($prev == 0.0) {
            return $current == 0.0 ? 0 : 100;
        }

        return (($current - $prev) / $prev) * 100;
    }

    private function parseStart(Request $request): Carbon
    {
        $start = $request->input('start');
        if ($start) return Carbon::parse($start)->startOfDay();
        return Carbon::now()->startOfMonth();
    }

    private function parseEnd(Request $request): Carbon
    {
        $end = $request->input('end');
        if ($end) return Carbon::parse($end)->endOfDay();
        return Carbon::now()->endOfMonth();
    }
}


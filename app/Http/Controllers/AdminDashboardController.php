<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCustomers = User::where('role', '!=', 'admin')->count();
        $totalRevenue = (float) Order::where('status', 'paid')->sum('total_amount');

        $todaySales = (float) Order::where('status', 'paid')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_amount');

        $monthlySalesAmount = (float) Order::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $estimatedProfit = (float) OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->selectRaw('COALESCE(SUM((price - COALESCE(cost_price, 0)) * quantity), 0) as profit')->value('profit');

        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function ($query) {
                $query->where('status', 'paid');
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $lowStockItems = collect();
        if (Schema::hasTable('inventory_items')) {
            $lowStockItems = InventoryItem::with('variant.masterProduct')
                ->whereColumn('quantity', '<=', 'min_stock')
                ->orderBy('quantity')
                ->limit(10)
                ->get();
        }

        $dailySeriesStart = now()->copy()->subDays(29)->startOfDay();
        $dailySalesRows = Order::where('status', 'paid')
            ->where('created_at', '>=', $dailySeriesStart)
            ->selectRaw('DATE(created_at) as sale_date, SUM(total_amount) as total')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->pluck('total', 'sale_date');

        $dailySalesChart = collect(range(0, 29))->map(function ($offset) use ($dailySeriesStart, $dailySalesRows) {
            $date = $dailySeriesStart->copy()->addDays($offset)->toDateString();

            return [
                'date' => $date,
                'label' => Carbon::parse($date)->format('d M'),
                'total' => (float) ($dailySalesRows[$date] ?? 0),
            ];
        })->values();

        $newOrderNotifications = Order::with('user')
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->limit(10)
            ->get();

        $monthlyRevenueByMonth = Order::where('status', 'paid')
            ->whereBetween('created_at', [now()->copy()->startOfMonth()->subMonths(5), now()->copy()->endOfMonth()])
            ->selectRaw('MONTH(created_at) as month_number, SUM(total_amount) as total')
            ->groupBy('month_number')
            ->pluck('total', 'month_number');

        $monthlySales = collect(range(0, 5))->map(function ($index) use ($monthlyRevenueByMonth) {
            $date = now()->copy()->subMonths(5 - $index);
            $monthNumber = (int) $date->format('n');

            return [
                'label' => $date->format('M'),
                'month_number' => $monthNumber,
                'total' => (float) $monthlyRevenueByMonth->get($monthNumber, 0),
                'is_current' => $date->isSameMonth(now()),
            ];
        })->values();

        // Ambil pesanan terbaru (5 terakhir)
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Prepare data for the view
        $stats = [
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_revenue' => $totalRevenue,
            'today_sales' => $todaySales,
            'monthly_sales_amount' => $monthlySalesAmount,
            'estimated_profit' => $estimatedProfit,
            'best_selling_products' => $bestSellingProducts,
            'low_stock_products' => $lowStockItems,
            'notifications' => [
                'new_orders' => $newOrderNotifications,
                'low_stock_count' => $lowStockItems->count(),
            ],
            'recent_orders' => $recentOrders,
            'monthly_sales' => $monthlySales,
            'daily_sales_chart' => $dailySalesChart,
        ];

        return view('movr.admin.dashboard', $stats);
    }

    public function report()
    {
        // Ambil data pendapatan 6 bulan terakhir untuk grafik
        $revenueData = Order::where('status', 'paid')
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%M') as month"),
                DB::raw('MAX(created_at) as sort_date')
            )
            ->groupBy('month')
            ->orderBy('sort_date', 'asc')
            ->get();

        // Ambil detail transaksi terbaru
        $incomeRecords = Order::with('user')
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');

        return view('movr.admin.report', [
            'revenueData' => $revenueData,
            'incomeRecords' => $incomeRecords,
            'totalRevenue' => $totalRevenue
        ]);
    }
}
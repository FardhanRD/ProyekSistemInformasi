<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardExportController extends Controller
{
    public function export(Request $request)
    {
        $start = $request->input('start') ? Carbon::parse($request->input('start'))->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->input('end') ? Carbon::parse($request->input('end'))->endOfDay() : Carbon::now()->endOfMonth();

        // CSV export (format sederhana & dependency-free). Bisa diganti menjadi XLSX/PDF nanti.
        $rows = Transaksi::query()
            ->select('kode_transaksi', 'pengguna_id', 'total_harga', 'status', 'tanggal')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $filename = 'admin_dashboard_export_' . $start->toDateString() . '_to_' . $end->toDateString() . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // header
            fputcsv($out, ['kode_transaksi', 'pengguna_id', 'total_harga', 'status', 'tanggal']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->kode_transaksi,
                    $r->pengguna_id,
                    $r->total_harga,
                    $r->status,
                    $r->tanggal,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}


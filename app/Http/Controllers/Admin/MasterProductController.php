<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\DetailProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class MasterProductController extends Controller
{
    public function index(Request $request)
    {
        $total_products = Produk::where('is_active', 1)->count();
        $total_categories = Kategori::where('is_active', 1)->count();

        $low_stock_alert = Produk::where('is_active', 1)
            ->whereHas('detailProduk', function ($q) {
                $q->whereRaw('stok <= (
                    SELECT stok_minimum FROM produk 
                    WHERE produk.produk_id = detail_produk.produk_id
                )')->where('stok', '>', 0);
            })->count();

        $inventory_value = DetailProduk::where('is_active', 1)
            ->selectRaw('SUM(harga * stok) as total')
            ->value('total') ?? 0;

        $popular_products = Produk::with('supplier')
            ->where('is_active', 1)
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        $max_terjual = $popular_products->max('total_terjual') ?: 1;

        $gender_distribution = Produk::selectRaw(
            'gender, COUNT(*) as total'
        )->where('is_active', 1)
            ->groupBy('gender')
            ->get();

        $search = $request->get('search');
        $status_filter = $request->get('status');
        $gender_filter = $request->get('gender');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $produk_list = Produk::with([
            'kategori',
            'supplier',
            'gambarUtama',
            'detailProduk' => fn($q) => $q->where('is_active', 1)
        ])
            ->where('is_active', 1)
            ->when($search, fn($q) => $q->where('nama_produk', 'like', "%{$search}%"))
            ->when($status_filter, fn($q) => $q->where('status_publish', $status_filter))
            ->when($gender_filter, fn($q) => $q->where('gender', $gender_filter))
            ->when($start_date && $end_date, function($q) use ($start_date, $end_date) {
                // Expecting yyyy-mm-dd
                $from = date('Y-m-d 00:00:00', strtotime($start_date));
                $to = date('Y-m-d 23:59:59', strtotime($end_date));
                return $q->whereBetween('created_at', [$from, $to]);
            })
            ->orderBy('penyimpanan_waktu', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.master-product.index', compact(
            'total_products',
            'total_categories',
            'low_stock_alert',
            'inventory_value',
            'popular_products',
            'max_terjual',
            'gender_distribution',
            'produk_list',
            'search',
            'status_filter',
            'gender_filter'
        ));
    }

    public function show($id)
    {
        $produk = Produk::with([
            'kategori',
            'supplier',
            'images',
            'detailProduk.warna',
            'ratings.buyer.pengguna',
        ])->findOrFail($id);

        return view('admin.master-product.detail', compact('produk'));
    }

    public function edit($id)
    {
        $produk = Produk::with(['kategori', 'supplier'])->findOrFail($id);
        $kategoris = Kategori::where('is_active', 1)->orderBy('urutan')->get();
        $suppliers = Supplier::where('is_verified', 1)->orderBy('nama_toko')->get();

        return view('admin.master-product.edit', compact('produk', 'kategoris', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'spesifikasi' => 'nullable|string',
            'gender' => 'required|in:men,women,unisex,kids',
            'tipe_olahraga' => 'nullable|string|max:80',
            'tags' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'status_publish' => 'required|in:publish,draft,scheduled',
            'scheduled_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'supplier_id' => 'required|exists:supplier,supplier_id',
        ]);

        $tags = collect(array_filter(array_map('trim', explode(',', (string) $request->input('tags', '')))))->values()->all();

        $produk->update([
            'nama_produk' => $validated['nama_produk'],
            'deskripsi' => $validated['deskripsi'],
            'spesifikasi' => $validated['spesifikasi'] ?? null,
            'gender' => $validated['gender'],
            'tipe_olahraga' => $validated['tipe_olahraga'] ?? null,
            'tags' => $tags ?: null,
            'harga_dasar' => $validated['harga_dasar'],
            'stok_minimum' => $validated['stok_minimum'],
            'status_publish' => $validated['status_publish'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'kategori_id' => $validated['kategori_id'],
            'supplier_id' => $validated['supplier_id'],
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.master-product.detail', $produk->produk_id)
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['is_active' => 0]);

        return redirect()->route('admin.master-product.index')
            ->with('success', 'Produk berhasil dinonaktifkan');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $status_filter = $request->get('status');
        $gender_filter = $request->get('gender');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $query = Produk::with(['kategori', 'supplier'])
            ->where('is_active', 1)
            ->when($search, fn($q) => $q->where('nama_produk', 'like', "%{$search}%"))
            ->when($status_filter, fn($q) => $q->where('status_publish', $status_filter))
            ->when($gender_filter, fn($q) => $q->where('gender', $gender_filter))
            ->when($start_date && $end_date, function($q) use ($start_date, $end_date) {
                $from = date('Y-m-d 00:00:00', strtotime($start_date));
                $to = date('Y-m-d 23:59:59', strtotime($end_date));
                return $q->whereBetween('created_at', [$from, $to]);
            })
            ->orderBy('penyimpanan_waktu', 'desc');

        $filename = 'master-product-export-' . date('YmdHis') . '.csv';

        $columns = ['ID', 'Nama Produk', 'Slug', 'Kategori', 'Supplier', 'Gender', 'Sport Type', 'Harga', 'Status', 'Created At'];

        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            // For PDF we will render a blade view and use dompdf
            $items = $query->get();
            $pdf = Pdf::loadView('admin.master-product.export', compact('items'));
            return $pdf->download('master-product-' . date('YmdHis') . '.pdf');
        }

        $callback = function() use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $query->chunk(200, function($items) use ($handle) {
                foreach ($items as $p) {
                    fputcsv($handle, [
                        $p->formatted_id ?? $p->produk_id,
                        $p->nama_produk,
                        $p->slug,
                        $p->kategori->nama_kategori ?? '-',
                        $p->supplier->nama_toko ?? '-',
                        $p->gender,
                        $p->tipe_olahraga ?? '-',
                        $p->harga_dasar ?? 0,
                        $p->status_publish ?? '-',
                        $p->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function events(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        // Normalize dates
        $startDate = $start ? date('Y-m-d', strtotime($start)) : null;
        $endDate = $end ? date('Y-m-d', strtotime($end)) : null;

        $events = [];

        // New products
        $prodQuery = Produk::query()->where('is_active', 1);
        if ($startDate && $endDate) {
            $prodQuery->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        }
        $newProducts = $prodQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        foreach ($newProducts as $row) {
            $events[] = [
                'date' => $row->date,
                'type' => 'new',
                'count' => $row->count,
            ];
        }

        // Campaigns (if table exists)
        if (Schema::hasTable('promo')) {
            $promoRows = DB::table('promo')
                ->selectRaw('DATE(mulai) as date, COUNT(*) as count')
                ->when($startDate && $endDate, fn($q) => $q->whereRaw("(DATE(mulai) BETWEEN ? AND ?)", [$startDate, $endDate]))
                ->groupBy('date')
                ->get();

            foreach ($promoRows as $p) {
                $events[] = [
                    'date' => $p->date,
                    'type' => 'campaign',
                    'count' => $p->count,
                ];
            }
        }

        return response()->json($events);
    }
}


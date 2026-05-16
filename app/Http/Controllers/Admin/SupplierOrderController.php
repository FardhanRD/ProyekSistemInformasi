<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderDetail;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\DetailProduk;
use App\Models\StockMovement;
use App\Models\Admin;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupplierOrderController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('supplier_order')) {
            return view('admin.supplier-order.index', [
                'orders' => collect(),
            ]);
        }

        $status = $request->get('status');
        $supplier_id = $request->get('supplier_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $orders = SupplierOrder::with(['supplier', 'admin.pengguna'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($supplier_id, fn($q) => $q->where('supplier_id', $supplier_id))
            ->when($start_date, fn($q) => $q->where('tanggal_order', '>=', $start_date . ' 00:00:00'))
            ->when($end_date, fn($q) => $q->where('tanggal_order', '<=', $end_date . ' 23:59:59'))
            ->orderBy('tanggal_order', 'desc')
            ->paginate(20)
            ->withQueryString();

        $supplier_list = Supplier::where('is_verified', 1)
            ->orderBy('nama_toko')
            ->get();

        return view('admin.supplier-order.index', [
            'orders' => $orders,
            'supplier_list' => $supplier_list,
            'status_filter' => $status,
            'supplier_filter' => $supplier_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::where('is_verified', 1)
            ->orderBy('nama_toko')
            ->get();

        $products = Produk::where('is_active', 1)
            ->orderBy('nama_produk')
            ->get();

        $detailProducts = DetailProduk::with(['produk', 'warna'])
            ->orderBy('detail_produk_id')
            ->get();

        return view('admin.supplier-order.create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'detailProducts' => $detailProducts,
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('=== STORE PO ===', $request->all());

        $request->validate([
            'supplier_id' => 'required|exists:supplier,supplier_id',
            'items' => 'required|array|min:1',
            'items.*.detail_produk_id' => 'required|exists:detail_produk,detail_produk_id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $admin = Admin::where('pengguna_id', auth()->user()->pengguna_id)->firstOrFail();
            $kode_order = 'PO-' . now()->format('Ymd') . '-' . str_pad(SupplierOrder::whereDate('tanggal_order', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            $totalHarga = 0;
            $totalItem = 0;

            $po = SupplierOrder::create([
                'supplier_id' => $request->get('supplier_id'),
                'admin_id' => $admin->admin_id,
                'kode_order' => $kode_order,
                'total_item' => 0,
                'total_harga' => 0,
                'status' => $request->get('status', 'draft'),
                'catatan' => $request->get('catatan'),
                'tanggal_order' => now(),
            ]);

            foreach ($request->get('items', []) as $item) {
                $subtotal = $item['qty'] * $item['harga_beli'];
                $totalHarga += $subtotal;
                $totalItem += $item['qty'];

                SupplierOrderDetail::create([
                    'supplier_order_id' => $po->supplier_order_id,
                    'detail_produk_id' => $item['detail_produk_id'],
                    'qty' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);
            }

            $po->update([
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
            ]);

            DB::commit();

            // Buat notifikasi untuk pengguna (admin yang membuat PO)
            try {
                if (Schema::hasTable('notifikasi')) {
                    Notifikasi::create([
                        'pengguna_id' => auth()->user()->pengguna_id,
                        'judul' => 'PO Baru Dibuat',
                        'pesan' => 'Purchase Order ' . $kode_order . ' berhasil dibuat',
                        'jenis' => 'transaksi',
                        'url_redirect' => '/admin/supplier-order',
                        'is_read' => 0,
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan proses utama jika notifikasi gagal
            }

            return redirect()->route('admin.supplier-order.show', $po->supplier_order_id)
                ->with('success', "PO {$kode_order} berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $po = SupplierOrder::with(['supplier', 'admin', 'details.detailProduk.produk'])
            ->findOrFail($id);

        return view('admin.supplier-order.show', [
            'po' => $po,
        ]);
    }

    public function receive($id)
    {
        $order = SupplierOrder::with('detail')->findOrFail($id);

        if ($order->status !== 'dikirim' && $order->status !== 'draft') {
            return back()->with('error', 'Status PO tidak valid');
        }

        DB::beginTransaction();

        try {
            // Update status PO
            $order->update([
                'status' => 'diterima',
                'tanggal_diterima' => now(),
            ]);

            // Loop setiap item PO
            foreach ($order->detail as $item) {
                $detail = DetailProduk::findOrFail($item->detail_produk_id);

                $stokSebelum = $detail->stok;
                $stokSesudah = $stokSebelum + $item->qty;

                // Update stok
                $detail->update(['stok' => $stokSesudah]);

                // Catat stock movement
                if (Schema::hasTable('stock_movement')) {
                    StockMovement::create([
                        'detail_produk_id' => $detail->detail_produk_id,
                        'jenis' => 'in',
                        'qty' => $item->qty,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSesudah,
                        'referensi' => $order->kode_order,
                        'catatan' => 'Stok masuk dari PO: ' . $order->kode_order,
                        'dibuat_oleh' => auth()->user()->pengguna_id,
                        'created_at' => now(),
                    ]);
                }
            }

            // Notifikasi
            if (Schema::hasTable('notifikasi')) {
                Notifikasi::create([
                    'pengguna_id' => auth()->user()->pengguna_id,
                    'judul' => 'Stok Diperbarui',
                    'pesan' => 'PO ' . $order->kode_order . ' diterima, stok bertambah',
                    'jenis' => 'transaksi',
                    'url_redirect' => '/admin/stock',
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.supplier-order.index')
                ->with('success', 'PO diterima, stok berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('GAGAL RECEIVE PO: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }


    public function invoicePdf($id)
    {
        $po = SupplierOrder::with(['supplier', 'admin', 'details.detailProduk.produk'])
            ->findOrFail($id);

        // Generate PDF (untuk sekarang return view, bisa di-upgrade dengan DomPDF nanti)
        return view('admin.supplier-order.invoice-pdf', [
            'po' => $po,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('pengguna') || !Schema::hasTable('buyer')) {
            return view('admin.customer.index', [
                'customers' => collect(),
            ]);
        }

        $search = $request->get('search');
        $status = $request->get('status');

        $customers = Pengguna::where('role', 'buyer')
            ->with(['buyer'])
            ->when($search, fn($q) => $q->where('nama_pengguna', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('no_telepon', 'like', "%{$search}%"))
            ->when($status !== null && $status !== '', fn($q) => $q->where('is_active', (bool)$status))
            ->orderBy('pengguna_id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Tambahkan info order per customer
        $customers->getCollection()->transform(function($customer) {
            $stats = DB::table('transaksi')
                ->where('pengguna_id', $customer->pengguna_id)
                ->selectRaw('COUNT(*) as total_order, COALESCE(SUM(total_harga), 0) as total_belanja')
                ->first();
            
            $customer->total_order = $stats->total_order ?? 0;
            $customer->total_belanja = $stats->total_belanja ?? 0;
            
            return $customer;
        });

        return view('admin.customer.index', [
            'customers' => $customers,
            'search_filter' => $search,
            'status_filter' => $status,
        ]);
    }

    public function block(Request $request, $id)
    {
        $customer = Pengguna::where('pengguna_id', $id)
            ->where('role', 'buyer')
            ->firstOrFail();

        $customer->update(['is_active' => !$customer->is_active]);

        $message = $customer->is_active ? 'Customer diaktifkan kembali.' : 'Customer berhasil diblokir.';

        return redirect()->route('admin.customer.index')
            ->with('success', $message);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $sort = $request->get('sort', 'recent');

        if (!DB::getSchemaBuilder()->hasTable('supplier')) {
            return view('admin.supplier.index', [
                'suppliers' => collect(),
                'search' => $q,
                'sort' => $sort,
                'categories' => collect(),
            ]);
        }

        $query = Supplier::query();

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('nama_toko', 'like', "%{$q}%")
                    ->orWhere('nama_owner', 'like', "%{$q}%")
                    ->orWhere('kategori_supplier', 'like', "%{$q}%");
            });
        }

        $query->when($sort === 'name_az', fn($qq) => $qq->orderBy('nama_toko', 'asc'));
        $query->when($sort === 'name_za', fn($qq) => $qq->orderBy('nama_toko', 'desc'));
        $query->when($sort === 'recent', fn($qq) => $qq->orderByDesc('created_at'));

        $suppliers = $query->paginate(12)->withQueryString();

        return view('admin.supplier.index', [
            'suppliers' => $suppliers,
            'search' => $q,
            'sort' => $sort,
            'categories' => DB::getSchemaBuilder()->hasTable('kategori') ? Kategori::where('is_active', 1)->orderBy('urutan')->get() : collect(),
        ]);
    }

    public function create()
    {
        $categories = DB::getSchemaBuilder()->hasTable('kategori') ? Kategori::where('is_active', 1)->orderBy('urutan')->get() : collect();

        return view('admin.supplier.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        if (!DB::getSchemaBuilder()->hasTable('supplier')) {
            return back()->with('error', 'Tabel supplier belum tersedia.');
        }

        $validated = $request->validate([
            'nama_toko' => 'required|string|max:255',
            'nama_owner' => 'required|string|max:255',
            'kategori_supplier' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'alamat_toko' => 'required|string|max:1000',
            'foto_toko' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:10240',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_toko')) {
            $fotoPath = $request->file('foto_toko')->store('suppliers', 'public');
        }

        $penggunaId = auth()->user()?->pengguna_id;
        if (! $penggunaId) {
            return back()->with('error', 'Akun admin tidak valid untuk membuat supplier.');
        }

        $supplier = Supplier::create([
            'pengguna_id' => $penggunaId,
            'nama_toko' => $validated['nama_toko'],
            'nama_owner' => $validated['nama_owner'],
            'kategori_supplier' => $validated['kategori_supplier'] ?? null,
            'no_telepon' => $validated['no_telepon'] ?? null,
            'email' => $validated['email'] ?? null,
            'alamat_toko' => $validated['alamat_toko'],
            'foto_toko' => $fotoPath,
            'is_verified' => 0,
        ]);

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show($id)
    {
        if (!DB::getSchemaBuilder()->hasTable('supplier')) {
            abort(404);
        }

        $supplier = Supplier::with(['produk'])->findOrFail($id);

        $produkList = DB::getSchemaBuilder()->hasTable('produk')
            ? Produk::where('supplier_id', $supplier->supplier_id)->where('is_active', 1)->with('kategori')->get()
            : collect();

        return view('admin.supplier.detail', [
            'supplier' => $supplier,
            'produkList' => $produkList,
        ]);
    }

    public function destroy($id)
    {
        if (!DB::getSchemaBuilder()->hasTable('supplier')) {
            return back()->with('error', 'Tabel supplier belum tersedia.');
        }

        $supplier = Supplier::findOrFail($id);
        // Tidak ada is_active pada supplier, pakai soft via is_verified=0
        $supplier->update(['is_verified' => 0]);

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier dihapus (nonaktif).');
    }
}


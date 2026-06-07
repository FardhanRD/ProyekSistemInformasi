<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

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

        // Tampilkan hanya supplier yang aktif/terverifikasi di daftar
        $query->where('is_verified', 1);

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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_toko' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:10240',
            'is_verified' => 'required|boolean',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_toko')) {
            $fotoPath = $request->file('foto_toko')->store('suppliers', 'public');
        }

        $email = $validated['email'];
        if ($email && Pengguna::where('email', $email)->exists()) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            return back()->withErrors(['email' => 'Email ini sudah digunakan oleh akun lain.'])->withInput();
        }

        if (!$email) {
            $slug = Str::slug($validated['nama_toko']) ?: 'supplier';
            do {
                $email = $slug . '-' . rand(1000, 9999) . '@example.com';
            } while (Pengguna::where('email', $email)->exists());
        }

        $slug = Str::slug($validated['nama_toko']) ?: 'supplier';
        do {
            $username = $slug . '-' . rand(100, 999);
        } while (Pengguna::where('username', $username)->exists());

        try {
            DB::beginTransaction();

            $pengguna = Pengguna::create([
                'nama_pengguna' => $validated['nama_owner'],
                'username' => $username,
                'email' => $email,
                'no_telepon' => $validated['no_telepon'] ?? '-',
                'sandi' => Hash::make('password'),
                'role' => 'supplier',
                'is_active' => 1,
            ]);

            $supplier = Supplier::create([
                'pengguna_id' => $pengguna->pengguna_id,
                'nama_toko' => $validated['nama_toko'],
                'nama_owner' => $validated['nama_owner'],
                'kategori_supplier' => $validated['kategori_supplier'] ?? null,
                'no_telepon' => $validated['no_telepon'] ?? null,
                'email' => $validated['email'] ?? null,
                'alamat_toko' => $validated['alamat_toko'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'foto_toko' => $fotoPath,
                'is_verified' => $validated['is_verified'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

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

        // Hapus file foto jika ada
        if ($supplier->foto_toko) {
            Storage::disk('public')->delete($supplier->foto_toko);
        }

        // Hapus permanen record supplier
        $supplier->delete();

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier telah dihapus.');
    }
}


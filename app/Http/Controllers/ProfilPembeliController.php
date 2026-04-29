<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilPembeliController extends Controller
{
    /**
     * Display the user's profile
     */
    public function index()
    {
        $user = auth()->user();
        $alamat = Alamat::where('pembeli_id', auth()->id())->get();
        
        return view('movr.profil.index', compact('user', 'alamat'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('profil.index')->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Kata sandi saat ini salah!');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profil.index')->with('status', 'Kata sandi berhasil diperbarui!');
    }

    /**
     * Store a new address
     */
    public function storeAlamat(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'detail_alamat' => 'required|string',
            'kode_pos' => 'required|string|max:10',
        ]);

        $pembeli_id = auth()->id();

    // Convert checkbox "on" into integer 1/0
    // $is_default = $request->has('is_default') ? 1 : 0;
    $is_default = $request->boolean('is_default') ? 1 : 0;

    $jumlahAlamat = Alamat::where('pembeli_id', $pembeli_id)->count();
    // dd("Jumlah alamat: " . $jumlahAlamat);
    // Jika alamat pertama → otomatis default
    if ($jumlahAlamat === 0) {
        $is_default = 1;
    }

    // Jika alamat baru diset default → reset yang lain
    // if ($is_default === 1) {
    //     Alamat::where('pembeli_id', $pembeli_id)->update(['is_default' => 0]);
    // }

    Alamat::create([
        'pembeli_id' => $pembeli_id,
        'label' => $request->label,
        'provinsi' => $request->provinsi,
        'kota' => $request->kota,
        'kecamatan' => $request->kecamatan,
        'detail_alamat' => $request->detail_alamat,
        'kode_pos' => $request->kode_pos,
        'is_default' => $is_default,
    ]);

    return redirect()->route('profil.index')->with('status', 'Alamat berhasil ditambahkan!');

    }
    // GET EXITING ADDRESS FOR EDITING
    public function edit($id)
    {
        $alamat = Alamat::findOrFail($id);
        return response()->json($alamat);
    }

    /**
     * Update an existing address
     */
    public function updateAlamat(Request $request, $id)
    {
        $alamat = Alamat::where('id', $id)->where('pembeli_id', auth()->id())->firstOrFail();

        $request->validate([
            'label' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'detail_alamat' => 'required|string',
            'kode_pos' => 'required|string|max:10',
        ]);

        // If this is set as default, update other addresses to not be default
        if ($request->boolean('is_default')) {
        // Reset semua alamat lain
        Alamat::where('pembeli_id', auth()->id())
            ->update(['is_default' => false]);

            $alamat->is_default = true;
        }

        $alamat->label = $request->label;
        $alamat->provinsi = $request->provinsi;
        $alamat->kota = $request->kota;
        $alamat->kecamatan = $request->kecamatan;
        $alamat->detail_alamat = $request->detail_alamat;
        $alamat->kode_pos = $request->kode_pos;

        $alamat->save();;

        return redirect()->route('profil.index')->with('status', 'Alamat berhasil diperbarui!');
    }

    /**
     * Remove an address
     */
    public function destroyAlamat($id)
    {
        $alamat = Alamat::where('id', $id)->where('pembeli_id', auth()->id())->firstOrFail();
        $alamat->delete();

        return redirect()->route('profil.index')->with('status', 'Alamat berhasil dihapus!');
    }
}
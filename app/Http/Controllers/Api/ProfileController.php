<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Ambil data profil dan alamat pengguna
     */
    public function index()
    {
        $user = auth()->user();
        $alamat = Alamat::where('pembeli_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'alamat' => $alamat
        ]);
    }

    /**
     * Update profil pengguna (nama, email, password, avatar)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Update password jika disediakan dan password saat ini benar
        if ($request->filled('password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->password);
        }

        // Upload avatar baru jika disediakan
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Tambah alamat baru untuk pengguna
     */
    public function storeAlamat(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
            'detail_alamat' => 'required|string',
            'kode_pos' => 'required|string',
        ]);

        $is_default = $request->boolean('is_default');
        
        // Set sebagai default jika alamat pertama atau diminta secara eksplisit
        if ($is_default || Alamat::where('pembeli_id', auth()->id())->count() === 0) {
            Alamat::where('pembeli_id', auth()->id())->update(['is_default' => 0]);
            $is_default = 1;
        }

        $alamat = Alamat::create([
            'pembeli_id' => auth()->id(),
            'label' => $request->label,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'detail_alamat' => $request->detail_alamat,
            'kode_pos' => $request->kode_pos,
            'is_default' => $is_default,
        ]);

        return response()->json(['status' => 'success', 'data' => $alamat]);
    }
    
    public function destroyAlamat($id)
    {
        $alamat = Alamat::where('id', $id)->where('pembeli_id', auth()->id())->firstOrFail();
        $alamat->delete();
        return response()->json(['status' => 'success', 'message' => 'Alamat dihapus']);
    }
}
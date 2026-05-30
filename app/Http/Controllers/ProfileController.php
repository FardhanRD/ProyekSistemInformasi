<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\AlamatPengguna;
use App\Models\Transaksi;
use App\Models\Buyer;
use App\Models\AkunPembayaran;
use App\Models\Wishlist;
use App\Services\PenggunaSyncService;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->first();
        $addresses = [];
        $orders = [];
        $paymentMethods = [];
        $semuaPesanan = collect();
        $orderCounts = [
            '' => 0,
            'menunggu_pembayaran' => 0,
            'pembayaran_dikonfirmasi' => 0,
            'dikirim' => 0,
            'selesai' => 0,
            'dibatalkan' => 0,
        ];

        if (Schema::hasTable('alamat_pengguna')) {
            $addresses = AlamatPengguna::where('pengguna_id', $user->pengguna_id)->get();
        }

        if (Schema::hasTable('transaksi')) {
            $orders = Transaksi::with([
                    'details.detailProduk.produk.gambarUtama',
                    'alamat',
                    'ekspedisi',
                    'pembayaran.metodePembayaran',
                    'pesanan.trackingLog',
                    'voucher',
                ])
                ->where('pengguna_id', $user->pengguna_id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $semuaPesanan = Transaksi::with([
                    'transaksiDetail.detailProduk.produk.gambarUtama',
                    'transaksiDetail.detailProduk.warna',
                ])
                ->where('pengguna_id', $user->pengguna_id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $orderCounts = [
                '' => $semuaPesanan->count(),
                'menunggu_pembayaran' => $semuaPesanan->where('status', 'menunggu_pembayaran')->count(),
                'pembayaran_dikonfirmasi' => $semuaPesanan->where('status', 'pembayaran_dikonfirmasi')->count(),
                'dikirim' => $semuaPesanan->where('status', 'dikirim')->count(),
                'selesai' => $semuaPesanan->where('status', 'selesai')->count(),
                'dibatalkan' => $semuaPesanan->where('status', 'dibatalkan')->count(),
            ];
        }

        if (Schema::hasTable('akun_pembayaran')) {
            $paymentMethods = AkunPembayaran::with('metodePembayaran')
                                ->where('pengguna_id', $user->pengguna_id)
                                ->get();
        }
        
        $availableMethods = \App\Models\MetodePembayaran::where('is_active', 1)->get();

        $orderCount = $semuaPesanan->count();
        $wishlistOwnerColumn = Wishlist::ownerColumn();
        $wishlistOwnerId = Wishlist::resolveOwnerId($user);
        $wishlistCount = $wishlistOwnerId ? Wishlist::where($wishlistOwnerColumn, $wishlistOwnerId)->count() : 0;

        $alamats = $addresses;
        $akunPembayaran = $paymentMethods;
        $metodePembayarans = $availableMethods;

        return view('buyer.profile.index', compact(
            'user',
            'buyer',
            'addresses',
            'orders',
            'paymentMethods',
            'availableMethods',
            'semuaPesanan',
            'orderCounts',
            'orderCount',
            'wishlistCount',
            'alamats',
            'akunPembayaran',
            'metodePembayarans'
        ));
    }

    public function updateProfile(Request $request, PenggunaSyncService $penggunaSyncService)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:pengguna,username,' . $user->pengguna_id . ',pengguna_id', // Assuming 'pengguna_id' is the primary key
            'email' => 'required|string|email|max:255|unique:pengguna,email,' . $user->pengguna_id . ',pengguna_id',
            'no_telepon' => 'nullable|string|max:25',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P', // Assuming 'L' for Male, 'P' for Female
            'foto_profil' => 'nullable|image|max:10240',
            'foto_profil_position' => 'nullable|string|max:50',
        ], [
            'foto_profil.max' => 'Ukuran foto profil maksimal adalah 10MB.',
            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.uploaded' => 'Gagal mengupload foto profil. Pastikan ukuran file tidak lebih dari 10MB.'
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->no_telepon = $validated['no_telepon'] ?? null;
        $user->tanggal_lahir = $validated['tanggal_lahir'] ?? null;
        $user->jenis_kelamin = $validated['jenis_kelamin'] ?? null;
        $user->foto_profil_position = $request->input('foto_profil_position', '50% 50%');

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            $file = $request->file('foto_profil');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($extension, ['heic', 'heif'])) {
                // Transcode HEIC to JPG using macOS native sips utility
                $tempPath = $file->getRealPath();
                $newFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time() . '.jpg';
                $targetPath = storage_path('app/public/profile/' . $newFilename);
                
                if (!file_exists(storage_path('app/public/profile'))) {
                    mkdir(storage_path('app/public/profile'), 0755, true);
                }
                
                $cmd = "sips -s format jpeg " . escapeshellarg($tempPath) . " --out " . escapeshellarg($targetPath);
                exec($cmd);
                
                if (file_exists($targetPath)) {
                    $user->foto_profil = 'profile/' . $newFilename;
                } else {
                    $user->foto_profil = $file->store('profile', 'public');
                }
            } else {
                $user->foto_profil = $file->store('profile', 'public');
            }
        }

        $user->save();

        $penggunaSyncService->ensureForAuthUser($user, 'buyer', [
            'nama_pengguna' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'no_telepon' => $user->no_telepon,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
            'foto_profil' => $user->foto_profil,
            'foto_profil_position' => $user->foto_profil_position,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function addresses()
    {
        return redirect()->to(route('profile.index') . '#addresses');
    }

    public function createAddress(Request $request)
    {
        // Ambil parameter 'return' dari URL dan teruskan ke view
        // agar bisa dimasukkan ke dalam form sebagai hidden input.
        return view('profile.address-form', ['return_url' => $request->query('return')]);
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:20',
            'nama_penerima' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kelurahan' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
            'alamat_lengkap' => 'required|string',
            'is_utama' => 'nullable|boolean',
        ]);

        $validated['pengguna_id'] = Auth::user()->pengguna_id;
        $validated['no_telepon'] = '+62' . ltrim(preg_replace('/\s+/', '', (string) $request->no_telepon), '0');

        // If this is marked as primary, unmark others
        if ($request->boolean('is_utama')) {
            AlamatPengguna::where('pengguna_id', Auth::user()->pengguna_id)->update(['is_utama' => false]);
            $validated['is_utama'] = true;
        } else {
            $validated['is_utama'] = false;
        }

        AlamatPengguna::create($validated);

        // Cek jika ada parameter 'return' untuk redirect kembali ke checkout
        if ($request->input('return') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Alamat baru berhasil ditambahkan!');
        }

        // Redirect default ke halaman daftar alamat
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil ditambahkan');
    }

    public function editAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        return view('profile.address-form', compact('address'));
    }

    public function updateAddress(Request $request, $id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:20',
            'nama_penerima' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'provinsi' => 'required|string|max:50',
            'kota' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kelurahan' => 'required|string|max:50',
            'kode_pos' => 'required|string|max:10',
            'alamat_lengkap' => 'required|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        // If this is marked as primary, unmark others
        if ($request->is_utama) {
            AlamatPengguna::where('pengguna_id', $user->pengguna_id)->where('alamat_id', '!=', $id)->update(['is_utama' => false]);
            $validated['is_utama'] = true;
        }

        $address->update($validated);

        // Menggunakan route name untuk konsistensi
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil diperbarui');
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $address->delete();

        // Menggunakan route name untuk konsistensi
        return redirect()->route('profile.addresses')->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set a specific address as primary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrimaryAddress($id)
    {
        $user = Auth::user();
        $address = AlamatPengguna::findOrFail($id);

        if ($address->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        DB::transaction(function () use ($user, $address) {
            AlamatPengguna::where('pengguna_id', $user->pengguna_id)->update(['is_utama' => false]);
            $address->update(['is_utama' => true]);
        });

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    /**
     * Show the saved payment methods.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function paymentMethods()
    {
        return redirect()->to(route('profile.index') . '#payment-methods');
    }

    /**
     * Store a new payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePaymentMethod(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'metode_id' => 'required|integer|exists:metode_pembayaran,metode_id',
            'nomor_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
        ]);

        AkunPembayaran::create([
            'pengguna_id' => $user->pengguna_id,
            'metode_id' => $validated['metode_id'],
            'nomor_akun' => $validated['nomor_akun'],
            'nama_akun' => $validated['nama_akun'],
        ]);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Delete a saved payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePaymentMethod($id)
    {
        $user = Auth::user();
        $account = AkunPembayaran::findOrFail($id);

        if ($account->pengguna_id != $user->pengguna_id) {
            abort(403, 'Unauthorized');
        }

        $account->delete();
        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    /**
     * Handle a change password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password lama tidak sesuai.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}

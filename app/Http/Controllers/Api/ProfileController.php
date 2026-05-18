<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\AlamatPengguna;
use App\Models\RatingToko;
use Illuminate\Http\Request;
class ProfileController extends Controller {
    public function index() {
        $user = auth()->user();
        if(!$user) return response()->json(['message'=>'Unauthenticated'], 401);
        $addresses = \App\Models\AlamatPengguna::where('pengguna_id', $user->pengguna_id ?? $user->id)
            ->orderBy('is_utama', 'desc')
            ->get();
        return response()->json([
            'user' => [
                'id' => $user->pengguna_id ?? $user->id,
                'name' => $user->nama_pengguna ?? $user->nama ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->no_telepon ?? '',
                'photo' => $user->foto_profil ? url('storage/'.$user->foto_profil) : null,
                'addresses' => $addresses
            ]
        ], 200);
    }
    public function update(Request $request) {
        $user = auth()->user();
        if(isset($user->nama_pengguna)) {
            $user->nama_pengguna = $request->name ?? $user->nama_pengguna;
        } else if(isset($user->nama)) {
            $user->nama = $request->name ?? $user->nama;
        }
        $user->email = $request->email ?? $user->email;
        $user->no_telepon = $request->phone ?? $request->no_hp ?? $user->no_telepon;
        if ($request->remove_photo == 'true') {
            $user->foto_profil = null;
        } else if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['heic', 'heif'])) {
                // Transcode HEIC to JPG using macOS native sips utility
                $tempPath = $file->getRealPath();
                $newFilename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.jpg';
                $targetPath = storage_path('app/public/profile_photos/' . $newFilename);

                if (!file_exists(storage_path('app/public/profile_photos'))) {
                    mkdir(storage_path('app/public/profile_photos'), 0755, true);
                }

                $cmd = "sips -s format jpeg " . escapeshellarg($tempPath) . " --out " . escapeshellarg($targetPath);
                exec($cmd);

                if (file_exists($targetPath)) {
                    $user->foto_profil = 'profile_photos/' . $newFilename;
                } else {
                    $user->foto_profil = $file->store('profile_photos', 'public');
                }
            } else {
                $user->foto_profil = $file->store('profile_photos', 'public');
            }
        }
        $user->save();
        return response()->json(['status'=>'success'], 200);
    }
    public function storeAlamat(Request $request) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        $data = $request->all();
        if(!isset($data['kelurahan'])) $data['kelurahan'] = '-';
        
        // If this is the first address, make it main
        $count = AlamatPengguna::where('pengguna_id', $pengguna_id)->count();
        $data['is_utama'] = ($count === 0);
        
        AlamatPengguna::create(array_merge($data, ['pengguna_id'=>$pengguna_id]));
        return response()->json(['status'=>'success'], 200);
    }
    public function updateAlamat(Request $request, $id) {
        $alamat = AlamatPengguna::where('alamat_id', $id)->first();
        if($alamat) {
            $data = $request->all();
            if(!isset($data['kelurahan'])) $data['kelurahan'] = '-';
            $alamat->update($data);
        }
        return response()->json(['status'=>'success'], 200);
    }
    public function setUtamaAlamat($id) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        AlamatPengguna::where('pengguna_id', $pengguna_id)->update(['is_utama' => false]);
        AlamatPengguna::where('alamat_id', $id)->where('pengguna_id', $pengguna_id)->update(['is_utama' => true]);
        return response()->json(['status'=>'success'], 200);
    }
    public function destroyAlamat($id) {
        AlamatPengguna::where('alamat_id', $id)->delete();
        return response()->json(['status'=>'success'], 200);
    }
    public function getStoreRating() {
        $avg = RatingToko::avg('rating') ?? 0;
        $count = RatingToko::count();
        return response()->json(['average_rating'=>(float)$avg, 'total_reviews'=>$count], 200);
    }
    public function postStoreRating(Request $request) {
        $pengguna_id = auth()->user()->pengguna_id ?? auth()->id();
        RatingToko::create(['rating'=>$request->rating, 'review'=>$request->review, 'pengguna_id'=>$pengguna_id]);
        return response()->json(['status'=>'success'], 200);
    }

    public function changePassword(Request $request) {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->sandi)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama yang Anda masukkan salah.'
            ], 422);
        }

        $user->sandi = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ], 200);
    }

    public function getPaymentAccounts() {
        $user = auth()->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $accounts = \App\Models\AkunPembayaran::with('metodePembayaran')
            ->where('pengguna_id', $user->pengguna_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $accounts->map(function ($acc) {
                return [
                    'id' => $acc->akun_pembayaran_id,
                    'nomor_akun' => $acc->nomor_akun,
                    'nama_akun' => $acc->nama_akun,
                    'is_active' => (bool)$acc->is_active,
                    'metode' => [
                        'id' => $acc->metode_id,
                        'name' => optional($acc->metodePembayaran)->metode ?? '',
                        'jenis' => optional($acc->metodePembayaran)->jenis ?? '',
                        'logo' => optional($acc->metodePembayaran)->logo_url ?? '',
                    ]
                ];
            })
        ], 200);
    }

    public function getActivePaymentMethods() {
        $methods = \App\Models\MetodePembayaran::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $methods
        ], 200);
    }

    public function storePaymentAccount(Request $request) {
        $request->validate([
            'metode_id' => 'required|integer|exists:metode_pembayaran,metode_id',
            'nomor_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $account = \App\Models\AkunPembayaran::create([
            'pengguna_id' => $user->pengguna_id,
            'metode_id' => $request->metode_id,
            'nomor_akun' => $request->nomor_akun,
            'nama_akun' => $request->nama_akun,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun pembayaran berhasil ditambahkan.',
            'data' => $account
        ], 201);
    }

    public function destroyPaymentAccount($id) {
        $user = auth()->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $account = \App\Models\AkunPembayaran::where('akun_pembayaran_id', $id)
            ->where('pengguna_id', $user->pengguna_id)
            ->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pembayaran tidak ditemukan.'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun pembayaran berhasil dihapus.'
        ], 200);
    }
}
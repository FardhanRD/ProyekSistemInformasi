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
}
<?php

namespace App\Http\Controllers\Api;

use App\Models\RatingProduk;
use App\Models\Produk;
use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index($id)
    {
        $reviews = RatingProduk::where('produk_id', $id)
            ->with(['buyer.pengguna'])
            ->get()
            ->map(function ($r) {
                $user = optional($r->buyer)->pengguna;
                $avatar = optional($user)->foto_profil ? url('storage/' . $user->foto_profil) : null;
                
                $photos = [];
                if ($r->foto_ulasan) {
                    $fotoList = is_string($r->foto_ulasan) ? json_decode($r->foto_ulasan, true) : $r->foto_ulasan;
                    if (is_array($fotoList)) {
                        foreach ($fotoList as $foto) {
                            $photos[] = url('storage/' . $foto);
                        }
                    }
                }

                $dateStr = $r->created_at ? (\Carbon\Carbon::parse($r->created_at)->format('d M Y')) : 'Baru saja';

                return [
                    'id' => $r->rating_id,
                    'rating' => $r->bintang,
                    'title' => $r->judul_ulasan ?? '',
                    'comment' => $r->isi_ulasan ?? '',
                    'reply' => $r->balasan,
                    'photos' => $photos,
                    'name' => optional($user)->nama_pengguna ?? optional($user)->nama ?? 'Anonim',
                    'avatar' => $avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional($user)->nama_pengguna ?? 'A') . '&background=random',
                    'date' => $dateStr,
                ];
            });

        $avgRating = RatingProduk::where('produk_id', $id)->avg('bintang') ?? 0.0;
        $totalReviews = RatingProduk::where('produk_id', $id)->count();

        $starsCount = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        if ($totalReviews > 0) {
            $grouped = RatingProduk::where('produk_id', $id)
                ->select('bintang', DB::raw('count(*) as total'))
                ->groupBy('bintang')
                ->get();
            foreach ($grouped as $g) {
                $starsCount[$g->bintang] = $g->total;
            }
        }

        return response()->json([
            'status' => 'success',
            'average_rating' => round((float)$avgRating, 1),
            'total_reviews' => $totalReviews,
            'stars_distribution' => $starsCount,
            'data' => $reviews
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|integer',
            'bintang' => 'required|integer|between:1,5',
            'judul_ulasan' => 'required|string|max:255',
            'isi_ulasan' => 'required|string|max:5000',
            'foto_ulasan' => 'nullable|array|max:3',
            'foto_ulasan.*' => 'image|mimes:jpeg,png,webp|max:2048'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        try {
            $user = auth()->user();
            $produk = Produk::findOrFail($request->produk_id);
            
            // Cek apakah user sudah membeli produk ini dan transaksi sudah selesai
            $hasPurchased = DB::table('transaksi_detail')
                ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
                ->join('detail_produk', 'transaksi_detail.detail_produk_id', '=', 'detail_produk.detail_produk_id')
                ->where('transaksi.pengguna_id', $user->pengguna_id)
                ->where('detail_produk.produk_id', $produk->produk_id)
                ->where('transaksi.status', 'selesai')
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus membeli produk ini terlebih dahulu untuk memberikan ulasan'
                ], 403);
            }

            // Cek apakah sudah pernah review
            $buyer = Buyer::where('pengguna_id', $user->pengguna_id)->firstOrFail();
            $alreadyReviewed = RatingProduk::where('produk_id', $produk->produk_id)
                ->where('buyer_id', $buyer->buyer_id)
                ->exists();

            if ($alreadyReviewed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memberikan ulasan untuk produk ini'
                ], 422);
            }

            // Handle foto upload
            $fotoArray = [];
            if ($request->hasFile('foto_ulasan')) {
                foreach ($request->file('foto_ulasan') as $file) {
                    $path = $file->store('reviews', 'public');
                    $fotoArray[] = $path;
                }
            }

            // Cari transaksi selesai terbaru yang mengandung produk ini
            $transaksiId = DB::table('transaksi_detail')
                ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.transaksi_id')
                ->join('detail_produk', 'transaksi_detail.detail_produk_id', '=', 'detail_produk.detail_produk_id')
                ->where('transaksi.pengguna_id', $user->pengguna_id)
                ->where('detail_produk.produk_id', $produk->produk_id)
                ->where('transaksi.status', 'selesai')
                ->orderByDesc('transaksi.transaksi_id')
                ->value('transaksi.transaksi_id');

            // Create rating
            $rating = RatingProduk::create([
                'produk_id' => $produk->produk_id,
                'buyer_id' => $buyer->buyer_id,
                'transaksi_id' => $transaksiId,
                'bintang' => $request->bintang,
                'judul_ulasan' => $request->judul_ulasan,
                'isi_ulasan' => $request->isi_ulasan,
                'foto_ulasan' => !empty($fotoArray) ? $fotoArray : null,
                'is_verified' => true,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil ditambahkan',
                'rating' => $rating
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

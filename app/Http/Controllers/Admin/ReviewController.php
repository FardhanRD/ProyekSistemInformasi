<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingProduk;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('rating_produk')) {
            return view('admin.review.index', [
                'reviews' => collect(),
                'stats' => [],
            ]);
        }

        $produk_id = $request->get('produk_id');
        $bintang = $request->get('bintang');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $reviews = RatingProduk::with(['produk', 'buyer.pengguna', 'penjawab'])
            ->when($produk_id, fn($q) => $q->where('produk_id', $produk_id))
            ->when($bintang, fn($q) => $q->where('bintang', $bintang))
            ->when($start_date, fn($q) => $q->where('created_at', '>=', $start_date . ' 00:00:00'))
            ->when($end_date, fn($q) => $q->where('created_at', '<=', $end_date . ' 23:59:59'))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Statistik
        $avgRating = RatingProduk::avg('bintang') ?? 0;
        $thisMonthReview = RatingProduk::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $lowRatings = RatingProduk::whereIn('bintang', [1, 2])->count();

        $stats = [
            'avg_rating' => round($avgRating, 1),
            'this_month_review' => $thisMonthReview,
            'low_ratings' => $lowRatings,
        ];

        return view('admin.review.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'produk_filter' => $produk_id,
            'bintang_filter' => $bintang,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function show($id)
    {
        $review = RatingProduk::with(['produk', 'buyer.pengguna', 'penjawab'])
            ->findOrFail($id);

        return view('admin.review.show', [
            'review' => $review,
        ]);
    }

    public function destroy($id)
    {
        $review = RatingProduk::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.review.index')
            ->with('success', 'Review berhasil dihapus.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'balasan' => 'required|string|min:10|max:1000',
        ]);

        $review = RatingProduk::findOrFail($id);
        
        $admin = Admin::where('pengguna_id', auth()->user()->pengguna_id)->firstOrFail();

        $review->update([
            'balasan' => $request->get('balasan'),
            'balas_oleh' => $admin->admin_id,
            'balas_tanggal' => now(),
        ]);

        $buyerId = $review->buyer?->pengguna_id;
        if ($buyerId) {
            kirimNotifikasi(
                $buyerId,
                'Ada balasan untuk review Anda',
                'Admin telah membalas review pada produk ' . ($review->produk?->nama_produk ?? '-'),
                'review',
                url('/produk/' . ($review->produk?->slug ?? ''))
            );
        }

        return redirect()->route('admin.review.index')
            ->with('success', 'Balasan review berhasil disimpan.');
    }
}

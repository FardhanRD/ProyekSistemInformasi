<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $items = collect(); // Initialize as Collection instead of array
        if ($user) {
            $ownerColumn = Wishlist::ownerColumn();
            $ownerId = Wishlist::resolveOwnerId($user);
            if ($ownerId) {
                $items = Wishlist::with(['produk.images'])->where($ownerColumn, $ownerId)->get();
            }
        }
        // Pastikan view selalu menerima variable `wishlists`
        return view('buyer.wishlist.index', ['wishlists' => $items]);
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
            }
            return redirect()->route('login');
        }
        $prodId = $request->input('produk_id');
        $ownerColumn = Wishlist::ownerColumn();
        $ownerId = Wishlist::resolveOwnerId($user);

        if (! $ownerId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akun belum tersinkron untuk wishlist.'], 422);
            }
            return back()->with('error', 'Akun belum tersinkron untuk wishlist.');
        }

        $exists = Wishlist::where($ownerColumn, $ownerId)->where('produk_id', $prodId)->first();
        if ($exists) {
            $exists->delete();
            $msg = 'Dihapus dari wishlist';
            $isWishlisted = false;
        } else {
            Wishlist::create([$ownerColumn => $ownerId, 'produk_id' => $prodId]);
            $msg = 'Ditambahkan ke wishlist';
            $isWishlisted = true;
        }

        if ($request->expectsJson() || $request->ajax() || $request->isJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_wishlisted' => $isWishlisted,
                'message' => $msg,
                'count' => Wishlist::where($ownerColumn, $ownerId)->count()
            ]);
        }

        return back()->with('success', $msg);
    }

    public function add(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|integer',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $ownerColumn = Wishlist::ownerColumn();
        $ownerId = Wishlist::resolveOwnerId($user);
        if (! $ownerId) {
            return response()->json(['success' => false, 'message' => 'Akun belum tersinkron untuk wishlist.'], 422);
        }

        Wishlist::firstOrCreate([
            $ownerColumn => $ownerId,
            'produk_id' => $request->integer('produk_id'),
        ]);

        return response()->json(['success' => true, 'message' => 'Ditambahkan ke wishlist']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|integer',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $ownerColumn = Wishlist::ownerColumn();
        $ownerId = Wishlist::resolveOwnerId($user);
        if (! $ownerId) {
            return response()->json(['success' => false, 'message' => 'Akun belum tersinkron untuk wishlist.'], 422);
        }

        Wishlist::where($ownerColumn, $ownerId)
            ->where('produk_id', $request->integer('produk_id'))
            ->delete();

        return response()->json(['success' => true, 'message' => 'Dihapus dari wishlist']);
    }
}

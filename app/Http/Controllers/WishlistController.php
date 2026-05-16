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
        return view('buyer.wishlist.index', compact('items'));
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();
        if (! $user) return redirect()->route('login');
        $prodId = $request->input('produk_id');
        $ownerColumn = Wishlist::ownerColumn();
        $ownerId = Wishlist::resolveOwnerId($user);

        if (! $ownerId) {
            return back()->with('error', 'Akun belum tersinkron untuk wishlist.');
        }

        $exists = Wishlist::where($ownerColumn, $ownerId)->where('produk_id', $prodId)->first();
        if ($exists) {
            $exists->delete();
            return back()->with('success','Dihapus dari wishlist');
        }
        Wishlist::create([$ownerColumn => $ownerId, 'produk_id' => $prodId]);
        return back()->with('success','Ditambahkan ke wishlist');
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

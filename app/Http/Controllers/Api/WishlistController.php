<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $produkId = $request->input('produk_id');
        $ownerColumn = Wishlist::ownerColumn();
        $ownerId = Wishlist::resolveOwnerId(auth()->user());

        if (!$produkId) {
            return response()->json(['success' => false, 'message' => 'produk_id required']);
        }

        if (!$ownerId) {
            return response()->json(['success' => false, 'message' => 'User mapping invalid'], 422);
        }

        $wishlist = Wishlist::where($ownerColumn, $ownerId)
            ->where('produk_id', $produkId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => true, 'added' => false, 'message' => 'Removed from wishlist']);
        } else {
            Wishlist::create([
                $ownerColumn => $ownerId,
                'produk_id' => $produkId
            ]);
            return response()->json(['success' => true, 'added' => true, 'message' => 'Added to wishlist']);
        }
    }
}

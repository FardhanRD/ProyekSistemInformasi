<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- PERBAIKAN: Mengarahkan langsung ke folder Controllers utama sesuai struktur repo ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\CartController; // Di screenshot namanyanya CartController.php di folder utama, atau KeranjangController jika ada. Sesuaikan jika error.
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishlistController; // Menggunakan WishlistController bawaan web temanmu

// SEMUA ROUTE KITA MASUKKAN KE DALAM 'v1'
Route::prefix('v1')->group(function () {

    // --- PUBLIC ROUTES (Bisa diakses tanpa login) ---
    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);
    Route::get('/products/category/{id}', [\App\Http\Controllers\Api\ProductController::class, 'getByCategory']);
    Route::get('/products/{id}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'index']);

    // --- AUTH ROUTES ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // --- PROTECTED ROUTES (Harus Login / Pakai Token) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Keranjang (Cart)
        Route::get('/cart', [\App\Http\Controllers\Api\CartController::class, 'index']);      
        Route::post('/cart', [\App\Http\Controllers\Api\CartController::class, 'add']);     
        Route::put('/cart/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']); 
        Route::delete('/cart/{id}', [\App\Http\Controllers\Api\CartController::class, 'remove']); 

        // Produk (CRUD)
        Route::post('/products', [\App\Http\Controllers\Api\ProductController::class, 'store']);
        Route::put('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'update']);
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'destroy']);

        // Kategori (CRUD)
        Route::post('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'store']);
        Route::put('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'destroy']);

        Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'index']);
        Route::put('/profile/update', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::post('/profile/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
        
        // Akun Pembayaran (Payment Accounts)
        Route::get('/profile/payment-accounts', [\App\Http\Controllers\Api\ProfileController::class, 'getPaymentAccounts']);
        Route::get('/profile/payment-methods', [\App\Http\Controllers\Api\ProfileController::class, 'getActivePaymentMethods']);
        Route::post('/profile/payment-accounts', [\App\Http\Controllers\Api\ProfileController::class, 'storePaymentAccount']);
        Route::delete('/profile/payment-accounts/{id}', [\App\Http\Controllers\Api\ProfileController::class, 'destroyPaymentAccount']);

        Route::post('/profile/alamat', [\App\Http\Controllers\Api\ProfileController::class, 'storeAlamat']);
        Route::put('/profile/alamat/{id}', [\App\Http\Controllers\Api\ProfileController::class, 'updateAlamat']);
        Route::put('/profile/alamat/{id}/utama', [\App\Http\Controllers\Api\ProfileController::class, 'setUtamaAlamat']);
        Route::delete('/profile/alamat/{id}', [\App\Http\Controllers\Api\ProfileController::class, 'destroyAlamat']);

        // Checkout Options
        Route::get('/checkout/options', [\App\Http\Controllers\Api\CheckoutController::class, 'options']);

        // Favorit / Wishlist (Menyelaraskan rute mobile dengan WishlistController bawaan web)
        Route::get('/favorites', [\App\Http\Controllers\Api\WishlistController::class, 'index']);
        Route::post('/favorites', [\App\Http\Controllers\Api\WishlistController::class, 'store']);
        Route::get('/favorites/check/{product_id}', [\App\Http\Controllers\Api\WishlistController::class, 'check']);
        Route::delete('/favorites/{id}', [\App\Http\Controllers\Api\WishlistController::class, 'destroy']);
        Route::delete('/favorites/product/{product_id}', [\App\Http\Controllers\Api\WishlistController::class, 'destroyByProduct']);
        Route::delete('/favorites/clear', [\App\Http\Controllers\Api\WishlistController::class, 'clear']);
        
        Route::get('/cart-debug', function() {
            $user = request()->user();
            $items = \App\Models\Keranjang::with(['detail.produk.images'])
                ->where('pengguna_id', $user->pengguna_id)
                ->get();
            $formatted = collect($items)->map(function ($item) {
                $produk = optional($item->detail)->produk;
                $imageUrl = $produk && $produk->images->first() ? $produk->images->first()->url_gambar : '';
                return [
                    'id' => $item->keranjang_id,
                    'jumlah' => $item->jumlah,
                    'harga_saat_ini' => optional($item->detail)->harga ?? optional($produk)->harga_dasar ?? 0,
                    'produk' => [
                        'id' => optional($produk)->produk_id ?? 0,
                        'name' => optional($produk)->nama_produk ?? 'Tanpa Nama',
                        'price' => optional($produk)->harga_dasar ?? 0,
                        'description' => optional($produk)->deskripsi ?? '',
                        'image' => $imageUrl
                    ]
                ];
            })->values();

            return response()->json([
                'user_id' => $user->pengguna_id,
                'items_count' => $items->count(),
                'success' => true,
                'data' => $formatted
            ]);
        });
    });
});
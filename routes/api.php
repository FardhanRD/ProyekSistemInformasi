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
        Route::get('/cart', [CartController::class, 'index']);      
        Route::post('/cart', [CartController::class, 'store']);     
        Route::put('/cart/{id}', [CartController::class, 'update']); 
        Route::delete('/cart/{id}', [CartController::class, 'destroy']); 

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
        Route::post('/profile/alamat', [\App\Http\Controllers\Api\ProfileController::class, 'storeAlamat']);
        Route::put('/profile/alamat/{id}', [\App\Http\Controllers\Api\ProfileController::class, 'updateAlamat']);
        Route::delete('/profile/alamat/{id}', [\App\Http\Controllers\Api\ProfileController::class, 'destroyAlamat']);

        // Favorit / Wishlist (Menyelaraskan rute mobile dengan WishlistController bawaan web)
        Route::get('/favorites', [\App\Http\Controllers\Api\WishlistController::class, 'index']);
        Route::post('/favorites', [\App\Http\Controllers\Api\WishlistController::class, 'store']);
        Route::get('/favorites/check/{product_id}', [\App\Http\Controllers\Api\WishlistController::class, 'check']);
        Route::delete('/favorites/{id}', [\App\Http\Controllers\Api\WishlistController::class, 'destroy']);
        Route::delete('/favorites/product/{product_id}', [\App\Http\Controllers\Api\WishlistController::class, 'destroyByProduct']);
        Route::delete('/favorites/clear', [\App\Http\Controllers\Api\WishlistController::class, 'clear']);
    });
});
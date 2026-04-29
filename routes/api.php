<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController; 
use App\Http\Controllers\Api\KeranjangController; 
use App\Http\Controllers\Api\ProfileController; // <--- PERBAIKAN: Baris ini WAJIB ada
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UlasanController;
use App\Http\Controllers\Api\FavoritController;

// SEMUA ROUTE KITA MASUKKAN KE DALAM 'v1'
Route::prefix('v1')->group(function () {

    // --- PUBLIC ROUTES (Bisa diakses tanpa login) ---
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/products/category/{id}', [ProductController::class, 'getByCategory']);
    Route::get('/products/{product_id}/reviews', [UlasanController::class, 'index']);
    Route::get('/reviews/{id}', [UlasanController::class, 'show']);

    // --- AUTH ROUTES ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // --- PROTECTED ROUTES (Harus Login / Pakai Token) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Keranjang
        Route::get('/cart', [KeranjangController::class, 'index']);      
        Route::post('/cart', [KeranjangController::class, 'store']);     
        Route::put('/cart/{id}', [KeranjangController::class, 'update']); 
        Route::delete('/cart/{id}', [KeranjangController::class, 'destroy']); 

        // Produk (CRUD) - hanya untuk user yang terautentikasi
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Kategori (CRUD) - hanya untuk user yang terautentikasi
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Profil & Alamat
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::put('/profile/update', [ProfileController::class, 'update']);
        Route::post('/profile/alamat', [ProfileController::class, 'storeAlamat']);
        Route::delete('/profile/alamat/{id}', [ProfileController::class, 'destroyAlamat']);

        // Ulasan (Reviews) CRUD
        Route::post('/products/{product_id}/reviews', [UlasanController::class, 'store']);
        Route::put('/reviews/{id}', [UlasanController::class, 'update']);
        Route::delete('/reviews/{id}', [UlasanController::class, 'destroy']);
        Route::get('/my-reviews', [UlasanController::class, 'myReviews']);

        // Favorit (Wishlist) CRUD
        Route::get('/favorites', [FavoritController::class, 'index']);
        Route::post('/favorites', [FavoritController::class, 'store']);
        Route::get('/favorites/check/{product_id}', [FavoritController::class, 'check']);
        Route::delete('/favorites/{id}', [FavoritController::class, 'destroy']);
        Route::delete('/favorites/product/{product_id}', [FavoritController::class, 'destroyByProduct']);
        Route::post('/favorites/clear', [FavoritController::class, 'clear']);
    });
});
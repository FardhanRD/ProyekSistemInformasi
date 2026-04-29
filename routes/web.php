<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response; // <--- PENTING: Untuk bikin respon gambar
use Illuminate\Support\Facades\File;     // <--- PENTING: Untuk baca file dari folder

use App\Http\Controllers\HalamanUtamaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfilPembeliController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\AdminProdukController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LogisticsController;
use App\Http\Controllers\Admin\MasterProductController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ProductPricingController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ReviewModerationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\AdminOrderController; 

/*
|--------------------------------------------------------------------------
| SOLUSI GAMBAR FLUTTER (ULTIMATE FIX)
|--------------------------------------------------------------------------
*/
Route::get('/image-proxy/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    if (!File::exists($filePath)) {
        $altPath = storage_path('app/public/products/' . $path);
        if (File::exists($altPath)) {
            $filePath = $altPath;
        } else {
            return Response::json(['message' => 'Image not found'], 404)
                ->header("Access-Control-Allow-Origin", "*");
        }
    }

    $file = File::get($filePath);
    $type = File::mimeType($filePath);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    $response->header("Access-Control-Allow-Origin", "*"); 
    $response->header("Access-Control-Allow-Methods", "GET, OPTIONS");

    return $response;
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HalamanUtamaController::class, 'index'])->name('home');
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');

// Authentication Route
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| MIDTRANS WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/webhook', [MidtransController::class, 'handleNotification'])->name('midtrans.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Keranjang routes
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::put('/keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    // Checkout Routes
    Route::post('/checkout/buy-now', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

    // Payment status
    Route::get('/payment/status/{orderId}', [MidtransController::class, 'paymentStatus'])->name('payment.status');

    // Profil routes
    Route::get('/profil', [ProfilPembeliController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilPembeliController::class, 'update'])->name('profil.update');
    Route::post('/profil/password', [ProfilPembeliController::class, 'updatePassword'])->name('profil.password.update');

    // Alamat routes
    Route::post('/profil/alamat', [ProfilPembeliController::class, 'storeAlamat'])->name('profil.alamat.store');
    Route::put('/profil/alamat/{id}', [ProfilPembeliController::class, 'updateAlamat'])->name('profil.alamat.update');
    Route::delete('/profil/alamat/{id}', [ProfilPembeliController::class, 'destroyAlamat'])->name('profil.alamat.destroy');
    Route::get('/profil/alamat/{id}', [ProfilPembeliController::class, 'edit'])->name('profil.alamat.edit');

    // Ulasan routes
    Route::get('/ulasan', function() { return redirect()->route('home'); }); // Redirect GET requests
    Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
    
    // Favorit routes
    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');
    Route::post('/favorit/toggle', [FavoritController::class, 'toggle'])->name('favorit.toggle');

    // Customer Dashboard routes
    Route::get('/dashboard/pelanggan', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/dashboard/pelanggan/order/{id}', [CustomerDashboardController::class, 'show'])->name('customer.order.show');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Role: Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Route Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Route Laporan Pendapatan (TAMBAHAN DISINI)
    Route::get('/laporan', [AdminDashboardController::class, 'report'])->name('report');

    // Route Orders
    Route::resource('orders', AdminOrderController::class)->except(['create', 'store']);

    // Structured Admin Panel API (Master Data + Operational Data)
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/products', [MasterProductController::class, 'index'])->name('products.index');
        Route::post('/products', [MasterProductController::class, 'store'])->name('products.store');
        Route::get('/products/{masterProduct}', [MasterProductController::class, 'show'])->name('products.show');
        Route::put('/products/{masterProduct}', [MasterProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{masterProduct}', [MasterProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/products/{masterProduct}/variants', [ProductVariantController::class, 'index'])->name('variants.index');
        Route::post('/products/{masterProduct}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::post('/products/{masterProduct}/variants/bulk-generate', [ProductVariantController::class, 'bulkGenerate'])->name('variants.bulk-generate');
        Route::put('/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');

        Route::get('/variants/{variant}/pricing', [ProductPricingController::class, 'index'])->name('pricing.index');
        Route::post('/variants/{variant}/pricing', [ProductPricingController::class, 'store'])->name('pricing.store');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
        Route::post('/supplier-products', [InventoryController::class, 'linkSupplierProduct'])->name('supplier-products.upsert');
        Route::put('/variants/{variant}/adjust', [InventoryController::class, 'adjust'])->name('variants.adjust');
        Route::get('/movements', [InventoryController::class, 'movements'])->name('movements.index');
    });

    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::post('/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('receive');
    });

    Route::prefix('order-management')->name('order-management.')->group(function () {
        Route::get('/', [OrderManagementController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderManagementController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('status');
        Route::post('/{order}/verify-payment', [OrderManagementController::class, 'verifyPayment'])->name('verify-payment');
        Route::put('/{order}/shipping', [OrderManagementController::class, 'updateShipping'])->name('shipping');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewModerationController::class, 'index'])->name('index');
        Route::put('/{ulasan}/moderate', [ReviewModerationController::class, 'moderate'])->name('moderate');
        Route::delete('/{ulasan}', [ReviewModerationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('promo')->name('promo.')->group(function () {
        Route::get('/vouchers', [PromoController::class, 'vouchers'])->name('vouchers.index');
        Route::post('/vouchers', [PromoController::class, 'storeVoucher'])->name('vouchers.store');
        Route::get('/discounts', [PromoController::class, 'discounts'])->name('discounts.index');
        Route::post('/discounts', [PromoController::class, 'storeDiscount'])->name('discounts.store');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/{user}/purchases', [UserManagementController::class, 'purchaseHistory'])->name('purchases');
        Route::put('/{user}/block', [UserManagementController::class, 'block'])->name('block');
        Route::put('/{user}/unblock', [UserManagementController::class, 'unblock'])->name('unblock');
    });

    Route::prefix('logistics')->name('logistics.')->group(function () {
        Route::get('/shipping-settings', [LogisticsController::class, 'shippingSettings'])->name('shipping-settings.index');
        Route::post('/shipping-settings', [LogisticsController::class, 'storeShippingSetting'])->name('shipping-settings.store');
        Route::put('/orders/{order}/tracking', [LogisticsController::class, 'updateOrderTracking'])->name('orders.tracking');
    });

    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/activities', [AuditLogController::class, 'activity'])->name('activities');
        Route::get('/audits', [AuditLogController::class, 'trails'])->name('audits');
    });

    // Route Kategori (Resource)
    Route::resource('kategori', CategoryController::class);

    // Route Supplier
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Route Admin Produk
    Route::get('/produk', [AdminProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/create', [AdminProdukController::class, 'create'])->name('produk.create');
    Route::get('/produk/{id}/variants', [AdminProdukController::class, 'variants'])->name('produk.variants');
        Route::post('/produk/{id}/variants', [AdminProdukController::class, 'storeVariants'])->name('produk.variants.store');
    Route::get('/produk/{id}', [AdminProdukController::class, 'show'])->name('produk.show');
    Route::post('/produk', [AdminProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}/edit', [AdminProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{id}', [AdminProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [AdminProdukController::class, 'destroy'])->name('produk.destroy');
});
<?php

namespace App\Providers;

use App\Models\AdminLog;
use App\Models\DetailProduk;
use App\Models\Ekspedisi;
use App\Models\Pengguna;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Promo;
use App\Models\Produk;
use App\Models\RatingProduk;
use App\Models\StockMovement;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderDetail;
use App\Models\Transaksi;
use App\Models\TrackingLog;
use App\Models\Voucher;
use App\Models\Notifikasi;
use App\Observers\AdminActivityObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Kategori;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Voucher::observe(AdminActivityObserver::class);
        Promo::observe(AdminActivityObserver::class);
        Ekspedisi::observe(AdminActivityObserver::class);
        Pengguna::observe(AdminActivityObserver::class);
        Transaksi::observe(AdminActivityObserver::class);
        Pembayaran::observe(AdminActivityObserver::class);
        Pesanan::observe(AdminActivityObserver::class);
        TrackingLog::observe(AdminActivityObserver::class);
        RatingProduk::observe(AdminActivityObserver::class);
        Produk::observe(AdminActivityObserver::class);
        DetailProduk::observe(AdminActivityObserver::class);
        StockMovement::observe(AdminActivityObserver::class);
        SupplierOrder::observe(AdminActivityObserver::class);
        SupplierOrderDetail::observe(AdminActivityObserver::class);

        // Share top-level active categories (with children) to all views
        try {
            View::composer('layouts.admin', function ($view) {
                $notifikasi = collect();

                if (auth()->check() && Schema::hasTable('notifikasi')) {
                    $notifikasi = Notifikasi::with('pengguna')
                        ->where('pengguna_id', auth()->user()->pengguna_id)
                        ->where('is_read', 0)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                }

                $view->with('notifikasi', $notifikasi);
            });

            $menuKategori = Kategori::with(['children.children'])
                ->whereNull('parent_id')
                ->where('is_active', 1)
                ->orderBy('urutan')
                ->get();

            View::share('menuKategori', $menuKategori);
        } catch (\Throwable $e) {
            // If something goes wrong (e.g., migrations not run yet), don't break the app
        }
    }
}

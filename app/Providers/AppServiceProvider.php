<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\TokoInfo;
use App\Models\Kategori;
use App\Models\Keranjang;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Hapus registrasi ItemViewController
        // $this->app->singleton(ItemViewController::class, function ($app) {
        //     return new ItemViewController();
        // });
    }

    /**
     * Bootstrap any application servic`        1es.
     */
    public function boot(): void
    {
        $tokoInfo = TokoInfo::first(); // Ambil data toko pertama (bisa disesuaikan dengan kondisi lain)

        // Membagikan data tokoInfo ke view 'admin.components.sidebar'
        View::composer(
            ['admin.components.sidebar', 'superadmin.components.sidebar', 'user.components.header', 'auth.login', 'auth.register'],
            function ($view) use ($tokoInfo) {
                $view->with('tokoInfo', $tokoInfo);
            }
        );
        // ambil semua kategori 
        $categories = Kategori::all();
        View::share('categories', $categories);

        // Hitung jumlah item keranjang untuk user yang login
        View::composer('*', function ($view) {
            $totalKeranjangItems = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $totalKeranjangItems = Keranjang::where('user_id', $user->id)->sum('jumlah');
            }

            $view->with('totalKeranjangItems', $totalKeranjangItems);
        });
    }
}

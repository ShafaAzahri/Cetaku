<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TokoInfo;

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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $tokoInfo = TokoInfo::first(); // Ambil data toko pertama (bisa disesuaikan dengan kondisi lain)

        // Membagikan data tokoInfo ke view 'admin.components.sidebar'
        View::composer(
            ['admin.components.sidebar', 'superadmin.components.sidebar'],
            function ($view) use ($tokoInfo) {
                $view->with('tokoInfo', $tokoInfo);
            }
        );
    }
}

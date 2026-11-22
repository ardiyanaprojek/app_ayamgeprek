<?php

namespace App\Providers;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('*', function ($view) {
            $shop = Schema::hasTable('shop_settings')
                ? ShopSetting::current()
                : (object) [
                    'nama_warung' => 'AYAM GEPREK ISTIQOMAH',
                    'alamat' => 'PADAKASIH',
                    'telepon' => null,
                ];

            $view->with('shopSetting', $shop);
        });
    }
}

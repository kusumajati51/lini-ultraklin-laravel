<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Validator;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('item_in_package', 'App\Validators\ItemValidator@validateItemInPackage');
        Validator::replacer('item_in_package', 'App\Validators\ItemValidator@messageItemInPackage');

        Validator::extend('can_order', 'App\Validators\SettingValidator@validateCanOrder');
        Validator::replacer('can_order', 'App\Validators\SettingValidator@messageCanOrder');

        Validator::extend('open_at', 'App\Validators\SettingValidator@validateOpenAt');
        Validator::replacer('open_at', 'App\Validators\SettingValidator@messageOpenAt');

        Validator::extend('package_in_region', 'App\Validators\PackageValidator@validatePackageInRegion');
        Validator::replacer('package_in_region', 'App\Validators\PackageValidator@messagePackageInRegion');

        Validator::extend('customer_in_client', 'App\Validators\CustomerValidator@validateCustomerInClient');
        Validator::replacer('customer_in_client', 'App\Validators\CustomerValidator@messageCustomerInClient');

        Validator::extend('can_create_store', 'App\Validators\StoreValidator@validateCanCreateStore');
        Validator::replacer('can_create_store', 'App\Validators\StoreValidator@messageCanCreateStore');

        if (env('APP_ENV') == 'live' || env('APP_ENV') == 'production') \URL::forceScheme('https');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('APP_DEBUG') !== 'true') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}

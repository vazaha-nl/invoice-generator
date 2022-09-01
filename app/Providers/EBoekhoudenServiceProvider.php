<?php

namespace App\Providers;

use App\Services\EBoekhouden\ApiClient;
use App\Services\EBoekhouden\SoapClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EBoekhoudenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SoapClient::class, function () {
            return new SoapClient([
                'trace' => config('e_boekhouden.debug'),
            ]);
        });

        $this->app->singleton(ApiClient::class, function (Application $app) {
            return new ApiClient(
                $app->make(SoapClient::class),
                config('e_boekhouden.username'),
                config('e_boekhouden.security_code1'),
                config('e_boekhouden.security_code2'),
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

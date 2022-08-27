<?php

namespace App\Providers;

use App\Services\ToggleTrack\ApiClient;
use App\Services\ToggleTrack\HttpClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ToggleTrackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HttpClient::class, function(Application $app) {
            return new HttpClient(
                config('toggl_track.base_uri'),
                config('toggl_track.api_token'),
            );
        });

        $this->app->singleton(ApiClient::class, function (Application $app) {
            return new ApiClient(
                $app->make(HttpClient::class),
                config('toggl_track.user_agent'),
                config('toggl_track.workspace_id'),
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

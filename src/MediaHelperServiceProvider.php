<?php

namespace AnisAronno\MediaHelper;

use Illuminate\Support\ServiceProvider;

class MediaHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('Media', function ($app) {
            return MediaHelpers::getInstance();
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

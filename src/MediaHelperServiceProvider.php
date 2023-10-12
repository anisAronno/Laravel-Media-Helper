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
        $this->registerConfig();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
           __DIR__.'/Config/media.php' => config_path('media.php'),
        ], 'media');

        $this->mergeConfigFrom(
            __DIR__.'/Config/media.php',
            'media'
        );
    }
}

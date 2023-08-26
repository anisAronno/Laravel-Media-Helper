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
        $this->registerMigration();
        $this->registerConfig();
    }


    protected function registerMigration()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->publishes([
            __DIR__ . '/Database/Migrations/2023_01_06_195610_create_images_table.php' => database_path('migrations/'. date('Y_m_d_His', time()).'_create_media_settings_table.php'),
        ], 'media-migration');
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

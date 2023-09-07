<?php

namespace AnisAronno\MediaHelper;

use AnisAronno\MediaHelper\RouteServiceProvider;
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

        $this->app->register(RouteServiceProvider::class);

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
            __DIR__ . '/Database/Migrations/2023_01_06_195610_create_images_table.php' => database_path('migrations/2023_01_06_195610_create_images_table.php'),
            __DIR__ . '/Database/Migrations/2023_02_11_174512_create_imageables_table.php' => database_path('migrations/2023_02_11_174512_create_imageables_table.php'),
        ], 'media-migration');

        $this->publishes([
            __DIR__ . '/Database/Factories/ImageFactory.php' => database_path('factories/ImageFactory.php'),
            __DIR__ . '/Database/Seeders/ImageSeeder.php' => database_path('seeders/ImageSeeder.php'),
        ], 'media-seed');
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

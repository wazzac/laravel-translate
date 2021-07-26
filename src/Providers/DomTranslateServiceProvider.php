<?php

namespace Wazza\DomTranslate\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Blade;
use Wazza\DomTranslate\Controllers\TranslateController;

class DomTranslateServiceProvider extends BaseServiceProvider
{
    /**
     * Publishes configuration file.
     * Allows us to run: // php artisan vendor:publish -tag=dom-translate-config
     *
     * @return  void
     */
    public function boot()
    {
        $this->publishes(
            [$this->configPath() => config_path('dom_translate.php')],
            'dom-translate-config'
        );

        $this->publishes([
            $this->dbMigrationsPath() => database_path('migrations')
        ], 'dom-translate-migrations');

        $this->loadMigrationsFrom($this->dbMigrationsPath());
    }

    /**
     * Make config publishment optional by merging the config from the package.
     * Name of the config file - config('dom_translate')
     *
     * @return  void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->configPath(),
            'dom_translate'
        );

        $this->app->singleton(TranslateController::class, function () {
            return new TranslateController();
        });

        Blade::directive('transl8', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
        });
    }

    /**
     * Set the config path
     *
     * @return string
     */
    private function configPath()
    {
        return __DIR__ . '/../../config/dom_translate.php';
    }

    /**
     * Set the db migration path
     *
     * @return string
     */
    private function dbMigrationsPath()
    {
        return __DIR__ . '/../../database/migrations';
    }
}

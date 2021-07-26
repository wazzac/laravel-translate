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

        // (1) Register the default Blade directives
        // With `transl8` you can supply any any destination language. If non is supplied, the default in Config would be used.
        // Format: transl8('Phrase','target','source')
        // Example: transl8('This must be translated to French.','fr')
        Blade::directive('transl8', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
        });

        // (2) Register direct (Language specific) Blade directives, all from English
        // (2.1) French Example: transl8fr('This must be translated to French.')
        Blade::directive('transl8fr', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "fr", "en");
        });
        // (2.2) German
        Blade::directive('transl8de', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "de", "en");
        });
        // (2.3) Japanese
        Blade::directive('transl8je', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "je", "en");
        });
        // (2.4) etc. You can create your own in Laravel AppServiceProvider register method.
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

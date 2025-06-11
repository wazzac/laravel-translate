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
    public function boot(): void
    {
        // Publish config files
        $this->publishes(
            [$this->configPath() => config_path('dom_translate.php')],
            'dom-translate-config'
        );

        // Publish migration files
        $this->publishes([
            $this->dbMigrationsPath() => database_path('migrations')
        ], 'dom-translate-migrations');

        // Load the migrations
        $this->loadMigrationsFrom($this->dbMigrationsPath());
    }

    /**
     * Make config publishment optional by merging the config from the package.
     * Name of the config file - config('dom_translate')
     *
     * @return  void
     */
    public function register(): void
    {
        // Merge the default config path
        $this->mergeConfigFrom(
            $this->configPath(),
            'dom_translate'
        );

        // Register the service the package provides.
        // As all the methods are static, we can use the controller directly.
        // Singleton registration will be considered later.
        /*
        $this->app->singleton(TranslateController::class, function () {
            return new TranslateController();
        });
        */

        // (1) Register the default Blade directives
        // With `transl8` you can supply any destination language. If none is supplied, the default in Config would be used.
        // Format: transl8('phrase','target','source')
        // Example: transl8('This must be translated to French.','fr')
        Blade::directive('transl8', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
        });

        // (2) Register direct (Language specific) Blade directives, all from English as a source
        // (2.1) French (fe)
        Blade::directive('transl8fr', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "fr", "en");
        });
        // (2.2) German (de)
        Blade::directive('transl8de', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "de", "en");
        });
        // (2.3) Japanese (je)
        Blade::directive('transl8je', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "je", "en");
        });
        // (2.4) Dutch (nl)
        Blade::directive('transl8nl', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "nl", "en");
        });
        // (2.5) Spanish (es)
        Blade::directive('transl8es', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "es", "en");
        });
        // (2.6) Italian (it)
        Blade::directive('transl8it', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "it", "en");
        });
        // (2.7) Portuguese (pt)
        Blade::directive('transl8pt', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "pt", "en");
        });
        // (2.8) Russian (ru)
        Blade::directive('transl8ru', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "ru", "en");
        });
        // (2.9) Chinese Simplefied (zh-CN)
        Blade::directive('transl8zhcn', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "zh-CN", "en");
        });
        // (2.10) Chinese Traditional (zh-TW)
        Blade::directive('transl8zhtw', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "zh-TW", "en");
        });
        // (2.11) Afrikaans (af)
        Blade::directive('transl8af', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "af", "en");
        });
        // (2.12) Arabic (ar)
        Blade::directive('transl8ar', function ($string) {
            return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "ar", "en");
        });
        // ... and many more, you can add your own in Laravel `AppServiceProvider` under the register method.
    }

    /**
     * Set the config path
     *
     * @return string
     */
    private function configPath(): string
    {
        return __DIR__ . '/../../config/dom_translate.php';
    }

    /**
     * Set the db migration path
     *
     * @return string
     */
    private function dbMigrationsPath(): string
    {
        return __DIR__ . '/../../database/migrations';
    }
}

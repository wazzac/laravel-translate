<?php

namespace Wazza\DomTranslate\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Blade;
use Wazza\DomTranslate\Helpers\TranslateHelper;
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

        // Load routes if enabled in config
        if (config('dom_translate.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        }

        // Register the SetLocale middleware if enabled in config
        if (config('dom_translate.middleware.auto_locale', true)) {
            $router = $this->app['router'];
            $router->aliasMiddleware('dom-translate.locale', \Wazza\DomTranslate\Http\Middleware\SetLocaleMiddleware::class);

            // Auto-apply to web middleware group if configured
            if (config('dom_translate.middleware.auto_apply', true)) {
                $router->pushMiddlewareToGroup('web', \Wazza\DomTranslate\Http\Middleware\SetLocaleMiddleware::class);
            }
        }
    }

    /**
     * Make config publication optional by merging the config from the package.
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

        // Register the service the package provides as a singleton.
        $this->app->singleton(TranslateController::class, function () {
            return new TranslateController();
        });

        // ---------------
        // Register the @transl8 directive for specific translation according to the config setup
        Blade::directive('transl8', function ($string) {
            return "<?= app(" . TranslateController::class . "::class)->phrase({$string}); ?>";
        });

        // ---------------
        // Register an auto translation directive that will use a session or cookie to determine the destination language
        Blade::directive('transl8auto', function ($string) {
            return "<?= \\Wazza\\DomTranslate\\Helpers\\TranslateHelper::autoTransl8({$string}, \\Wazza\\DomTranslate\\Helpers\\TranslateHelper::currentDefinedLanguageCode()); ?>";
        });

        // ---------------
        // Register the @transl8 directive for specific languages you use often
        $languages = [
            'fr', // French
            'de', // German
            'nl', // Dutch
            'es', // Spanish
            'it', // Italian
            'pt', // Portuguese
            'ru', // Russian
            'zhcn' => 'zh-CN', // Chinese Simplified
            'zhtw' => 'zh-TW', // Chinese Traditional
            'af', // Afrikaans
            'ar' => 'ar-SA', // Arabic
            // ... Add more languages as needed
        ];

        // Register directives for each language by iterating over the languages array
        foreach ($languages as $alias => $langCode) {
            // Handle array values like 'zhcn' => 'zh-CN'
            $directive = is_string($alias) ? $alias : $langCode;
            Blade::directive("transl8{$directive}", function ($string) use ($langCode) {
                return "<?= app(" . TranslateController::class . "::class)->translate({$string}, '{$langCode}', 'en'); ?>";
            });
        }
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

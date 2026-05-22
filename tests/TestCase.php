<?php

namespace Wazza\DomTranslate\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Add the package provider.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            \Wazza\DomTranslate\Providers\DomTranslateServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', env('APP_KEY', 'base64:2fl+Ktvkfl+Ktvkfl+Ktvkfl+Ktvkfl+Ktvkfl+Ktvk='));
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('dom_translate.logging.level', env('DOM_TRANSLATE_LOG_LEVEL', 0));
        $app['config']->set('dom_translate.logging.indicator', env('DOM_TRANSLATE_LOG_INDICATOR', 'dom-translate-test'));
        $app['config']->set('dom_translate.api.provider', env('DOM_TRANSLATE_PROVIDER', 'google'));
        $app['config']->set('dom_translate.api.google.key', env('DOM_TRANSLATE_GOOGLE_KEY'));
    }

    /**
     * Define aliases for the package.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'config' => \Illuminate\Config\Repository::class,
        ];
    }
}

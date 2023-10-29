<?php

namespace Wazza\DomTranslate\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    /**
     * Add the package provider
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Wazza\DomTranslate\Providers\DomTranslateServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/testdb.sqlite',
            'prefix' => '',
        ]);
        $app['config']->set('dom_translate.logging.level', env('DOM_TRANSLATE_LOG_LEVEL'));
        $app['config']->set('dom_translate.logging.indicator', env('DOM_TRANSLATE_LOG_INDICATOR'));
        $app['config']->set('dom_translate.api.provider', env('DOM_TRANSLATE_PROVIDER'));
        $app['config']->set('dom_translate.api.google.key', env('DOM_TRANSLATE_GOOGLE_KEY'));
    }

    /**
     * Define aliases for the package
     */
    protected function getPackageAliases($app)
    {
        return [
            'config' => 'Illuminate\Config\Repository'
        ];
    }
}

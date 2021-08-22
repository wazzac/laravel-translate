<?php

namespace Wazza\DomTranslate\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Wazza\DomTranslate\Providers\DomTranslateServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        $app['config']->set('dom_translate.logging.level', 0);
        $app['config']->set('dom_translate.logging.indicator', 'test-logs');
    }

    protected function getPackageAliases($app)
    {
        return [
            'config' => 'Illuminate\Config\Repository'
        ];
    }
}

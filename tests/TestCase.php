<?php

namespace Labdacaraka\WebInstaller\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Labdacaraka\WebInstaller\WebInstallerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Labdacaraka\\WebInstaller\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            WebInstallerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_web-installer_table.php.stub';
        $migration->up();
        */
    }
}

<?php

namespace Labdacaraka\WebInstaller;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Labdacaraka\WebInstaller\Commands\WebInstallerCommand;

class WebInstallerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('web-installer')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasRoutes(['web'])
            ->hasMigration('create_web-installer_table')
            ->hasCommand(WebInstallerCommand::class);
    }
}

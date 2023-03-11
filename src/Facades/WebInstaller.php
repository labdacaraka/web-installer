<?php

namespace Labdacaraka\WebInstaller\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Labdacaraka\WebInstaller\WebInstaller
 */
class WebInstaller extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Labdacaraka\WebInstaller\WebInstaller::class;
    }
}

<?php

namespace Spatie\DynamicServers;

use Illuminate\Support\Facades\Config;
use Spatie\DynamicServers\Exceptions\JobDoesNotExist;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynamicServersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dynamic-servers')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_dynamic_servers_table');
    }
}

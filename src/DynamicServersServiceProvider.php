<?php

namespace Spatie\DynamicServers;

use Spatie\DynamicServers\Commands\DetectHangingServersCommand;
use Spatie\DynamicServers\Commands\ListDynamicServersCommand;
use Spatie\DynamicServers\Commands\MonitorDynamicServersCommand;
use Spatie\DynamicServers\Support\Config;
use Spatie\DynamicServers\Support\DynamicServersManager;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use Spatie\DynamicServers\Support\ServerTypes\ServerTypes;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynamicServersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dynamic-servers')
            ->hasConfigFile()
            ->hasMigration('create_dynamic_servers_table')
            ->publishesServiceProvider('DynamicServersServiceProvider')
            ->hasCommands(
                DetectHangingServersCommand::class,
                ListDynamicServersCommand::class,
                MonitorDynamicServersCommand::class,
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('spatie/laravel-dynamic-servers')
                    ->endWith(function (InstallCommand $installCommand) {
                        $installCommand->info('You can view all docs at https://spatie.be/docs/laravel-dynamic-servers');
                        $installCommand->line('');
                        $installCommand->info('Thank you very much for installing this package');
                    });
            });
    }

    public function packageRegistered()
    {
        $this->app->singleton(DynamicServersManager::class, fn () => new DynamicServersManager());
        $this->app->bind('dynamicServers', DynamicServersManager::class);

        $this->app->singleton(ServerTypes::class, fn () => new ServerTypes());

        $this->registerDefaultServerType();
    }

    protected function registerDefaultServerType(): self
    {
        $defaultType = ServerType::new('default')
            ->provider(Config::defaultProviderName());

        app(DynamicServersManager::class)->registerServerType($defaultType);

        return $this;
    }
}

<?php

namespace Spatie\DynamicServers\Tests\TestSupport;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\DynamicServers\DynamicServersServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\DynamicServers\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelDataServiceProvider::class,
            DynamicServersServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../../database/migrations/create_dynamic_servers_table.php';

        $migration->up();
    }

    protected function loadEnvironmentVariables()
    {
        if (! file_exists(__DIR__.'/../../.env')) {
            return;
        }

        $dotEnv = Dotenv::createImmutable(__DIR__.'/../..');

        $dotEnv->load();
    }

    protected function processQueuedJobs()
    {
        foreach (Queue::pushedJobs() as $job) {
            app()->call([$job[0]['job'], 'handle']);
        }
    }
}

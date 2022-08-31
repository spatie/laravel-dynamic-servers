<?php

namespace Spatie\DynamicServers\Tests\TestSupport;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\DynamicServers\DynamicServersServiceProvider;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        $this->setUpUpCloudTestProvider();

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

    protected function setUpUpCloudTestProvider(): self
    {
        DynamicServers::registerServerType(
            ServerType::new('up_cloud')
                ->provider('up_cloud')
                ->configuration(function(Server $server) {
                    return [
                        'server' => [
                            'zone' => 'de-fra1',
                            'title' => $server->name,
                            'hostname' => Str::slug($server->name),
                            'plan' => '2xCPU-4GB',
                            'storage_devices' => [
                                'storage_device' => [
                                    [
                                        'action' => 'clone',
                                        'storage' => $server->option('disk_image'),
                                        'title' => Str::slug($server->name).'-disk',
                                        'tier' => 'maxiops',
                                    ],
                                ],
                            ],
                        ],
                    ];
                })
        );

        return $this;
    }
}

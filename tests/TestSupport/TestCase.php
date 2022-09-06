<?php

namespace Spatie\DynamicServers\Tests\TestSupport;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\DynamicServers\DynamicServersServiceProvider;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\ServerProviders\ServerProvider;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use Spatie\DynamicServers\Tests\TestSupport\ServerProviders\DummyServerProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        $this
            ->setUpUpCloudTestProvider();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\DynamicServers\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
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
        static $executedJobIds = [];

        foreach (Queue::pushedJobs() as $job) {
            $jobObject = $job[0]['job'];

            if (in_array(spl_object_id($jobObject), $executedJobIds)) {
                continue;
            }

            if ($jobObject->server) {
                $jobObject->server = $jobObject->server->refresh();
            }

            app()->call([$jobObject, 'handle']);

            $executedJobIds[] = spl_object_id($jobObject);
        }
    }

    protected function setUpUpCloudTestProvider(): self
    {
        $this->setDefaultServerProvider(DummyServerProvider::class);

        $providerConfig = config('dynamic-servers.providers');
        $providerConfig['other_provider'] = [
            'class' => DummyServerProvider::class,
        ];

        config()->set('dynamic-servers.providers', $providerConfig);

        DynamicServers::registerServerType(ServerType::new('other')->provider('other_provider'));

        return $this;
    }

    /**
     * @param  class-string<ServerProvider>  $serverProvider
     * @return $this
     */
    protected function setDefaultServerProvider(string $serverProvider): self
    {
        config()->set('dynamic-servers.providers.up_cloud.class', $serverProvider);

        return $this;
    }

    public function upCloudHasBeenConfigured(): bool
    {
        return config()->has('dynamic-servers.providers.up_cloud.options.username');
    }
}

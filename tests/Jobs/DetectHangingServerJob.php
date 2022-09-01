<?php

namespace Spatie\DynamicServers\Tests\Jobs;

use Illuminate\Support\Facades\Event;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerHangingEvent;
use Spatie\DynamicServers\Jobs\VerifyServerDeletedJob;
use Spatie\DynamicServers\Jobs\VerifyServerStartedJob;
use Spatie\DynamicServers\Jobs\VerifyServerStoppedJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Tests\TestSupport\ServerProviders\EndlessDeleteServerProvider;
use Spatie\DynamicServers\Tests\TestSupport\ServerProviders\EndlessStartServerProvider;
use Spatie\DynamicServers\Tests\TestSupport\ServerProviders\EndlessStopServerProvider;
use function Spatie\PestPluginTestTime\testTime;

it('can detect that the server is hanging when stopping takes too long', function(
    string $serverProviderClass,
    string $jobClass,
    ServerStatus $serverStatus) {
    $this->setDefaultServerProvider($serverProviderClass);

    Event::fake();

    testTime()->freeze();

    $server = Server::factory()->create()->markAs($serverStatus);
    $verifyServerStartedJob = new $jobClass($server);

    testTime()->addMinutes(config('dynamic-servers.mark_server_as_hanging_after_minutes'))->subSecond();
    dispatch($verifyServerStartedJob);
    expect($server->refresh()->status)->toBe($serverStatus);

    testTime()->addHour(); // adding an hour so job uniqueness does not get in the way
    dispatch($verifyServerStartedJob);
    expect($server->refresh()->status)->toBe(ServerStatus::Hanging);

    Event::assertDispatched(function(ServerHangingEvent $event) use ($serverStatus) {
        expect($event->previousStatus)->toBe($serverStatus);

        return true;
    });
})->with([
    [EndlessStartServerProvider::class, VerifyServerStartedJob::class, ServerStatus::Starting],
    [EndlessStopServerProvider::class, VerifyServerStoppedJob::class, ServerStatus::Stopping],
    [EndlessDeleteServerProvider::class, VerifyServerDeletedJob::class, ServerStatus::Deleting],

]);

<?php

namespace Spatie\DynamicServers\Tests\Jobs;

use Illuminate\Support\Facades\Event;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerHangingEvent;
use Spatie\DynamicServers\Jobs\VerifyServerStartedJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Tests\TestSupport\ServerProviders\EndlessStartServerProvider;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->setDefaultServerProvider(EndlessStartServerProvider::class);

    Event::fake();
});

it('can detect that the server is hanging when starting takes too long', function () {
    testTime()->freeze();

    $server = Server::factory()->create()->markAs(ServerStatus::Starting);
    $verifyServerStartedJob = new VerifyServerStartedJob($server);

    testTime()->addMinutes(config('dynamic-servers.mark_server_as_hanging_after_minutes'))->subSecond();
    dispatch($verifyServerStartedJob);
    expect($server->refresh()->status)->toBe(ServerStatus::Starting);

    testTime()->addHour(); // adding an hour so job uniqueness does not get in the way
    dispatch($verifyServerStartedJob);
    expect($server->refresh()->status)->toBe(ServerStatus::Hanging);

    Event::assertDispatched(function (ServerHangingEvent $event) {
        expect($event->previousStatus)->toBe(ServerStatus::Starting);

        return true;
    });
});

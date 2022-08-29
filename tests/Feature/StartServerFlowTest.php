<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Jobs\VerifyServerStartedJob;
use Spatie\DynamicServers\Models\Server;

beforeEach(function () {
    $serverResponse = getStub('server', [
        'server.uuid' => 'fake-uuid',
    ]);

    $startingServer = getStub('server', [
        'server.uuid' => 'fake-uuid',
        'server.state' => 'maintenance',
    ]);

    $startedServer = getStub('server', [
        'server.uuid' => 'fake-uuid',
        'server.state' => 'started',
    ]);

    Http::preventStrayRequests()
        ->fake([
            'https://api.upcloud.com/1.3/server' => Http::response($serverResponse),
            'https://api.upcloud.com/1.3/server/fake-uuid' => Http::sequence([
                Http::response($startingServer),
                Http::response($startedServer),
            ]),
        ]);

    Queue::fake();

    /** @var Server $server */
    $this->server = Server::factory()->create([
        'name' => 'my-server',
        'provider' => 'up_cloud',
    ]);
});

it('can start a server', function() {
    $this->server->start();
    Queue::assertPushed(CreateServerJob::class);
    expect($this->server->refresh()->status)->toBe(ServerStatus::Starting);

    processQueuedJobs();
    Queue::assertPushed(VerifyServerStartedJob::class);
    expect($this->server->refresh()->status)->toBe(ServerStatus::Starting);

    processQueuedJobs();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Starting);

    processQueuedJobs();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Running);
});

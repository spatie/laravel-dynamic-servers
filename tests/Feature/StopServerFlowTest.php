<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Jobs\StopServerJob;
use Spatie\DynamicServers\Models\Server;

beforeEach(function () {
    $serverResponse = getStub('server', [
        'server.uuid' => 'fake-uuid',
    ]);

    $stoppingServer = getStub('server', [
        'server.uuid' => 'fake-uuid',
        'server.state' => 'maintenance',
    ]);

    $stoppedServer = getStub('server', [
        'server.uuid' => 'fake-uuid',
        'server.state' => 'stopped',
    ]);

    Http::preventStrayRequests()
        ->fake([
            'https://api.upcloud.com/1.3/server' => Http::response($serverResponse),
            'https://api.upcloud.com/1.3/server/fake-uuid/stop' => Http::response($stoppingServer),
            'https://api.upcloud.com/1.3/server/fake-uuid' => Http::response($stoppedServer),
            'https://api.upcloud.com/1.3/server/fake-uuid?storages=1&backups=delete' => Http::response($stoppingServer),
        ]);

    Queue::fake();

    /** @var Server $server */
    $this->server = Server::factory()->create([
        'name' => 'my-server',
        'provider' => 'up_cloud',
        'status' => ServerStatus::Running,
    ]);

    $this->server->addMeta('server_properties', getStub('server', [
        'server.uuid' => 'fake-uuid',
        'server.state' => 'running',
    ])['server']);
});

it('can stop a server', function () {
    $this->server->stop();

    Queue::assertPushed(StopServerJob::class);
    expect($this->server->refresh()->status)->toBe(ServerStatus::Stopping);

    $this->processQueuedJobs();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Stopping);

    $this->processQueuedJobs();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Stopped);

    $this->processQueuedJobs();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Deleting);

    $this->processQueuedJobs();
    ray($this->server->refresh());
    expect($this->server->refresh()->status)->toBe(ServerStatus::Deleted);
});

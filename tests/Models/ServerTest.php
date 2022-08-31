<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Exceptions\CannotStartServer;
use Spatie\DynamicServers\Exceptions\InvalidProvider;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\ServerProviders\ServerProvider;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;

beforeEach(function () {
    /** @var Server server */
    $this->server = Server::factory()->create();

    Queue::fake();
});

it('can add meta data to a server', function (mixed $value) {
    $this->server->addMeta('key', $value);

    expect($this->server->refresh()->meta['key'])->toEqual($value);
})->with([
    'a string',
    ['an', 'array', 'of', 'strings'],
    true,
    1,
]);

it('can merge meta data of a server', function () {
    $this->server->addMeta('original_key', 'original_value');
    $this->server->addMeta('another_key', 'another_value');

    expect($this->server->refresh()->meta->toArray())->toEqual([
        'original_key' => 'original_value',
        'another_key' => 'another_value',
    ]);
});

it('can mark a server as errored', function () {
    $this->server->markAsErrored(new Exception('This is an exception'));

    expect($this->server)
        ->status->toBe(ServerStatus::Errored)
        ->exception_class->toBe(Exception::class)
        ->exception_message->toBe('This is an exception')
        ->exception_trace->toBeString();
});

it('can get a provider', function () {
    expect($this->server->serverProvider())->toBeInstanceOf(ServerProvider::class);
});

it('will throw an exception when using an unknown provider', function () {
    $this->server->update([
        'provider' => 'unknown_provider',
    ]);

    expect($this->server->serverProvider());
})->throws(InvalidProvider::class);

it('will dispatch a job to start a server', function () {
    $this->server->start();

    Queue::assertPushed(CreateServerJob::class);
});

it('will throw an exception if the server is not in the right state to be started', function () {
    $this->server->update([
        'status' => ServerStatus::Starting,
    ]);

    $this->server->start();
})->throws(CannotStartServer::class);

it('can prepare a server for the default provider', function () {
    $server = Server::prepareNew();

    expect($server)
        ->type->toBe('default')
        ->status->toBe(ServerStatus::New);
});

it('can prepare a server for another provider', function () {
    $server = Server::prepareNew('other');

    expect($server)
        ->type->toBe('other')
        ->provider->toBe('other_provider')
        ->status->toBe(ServerStatus::New);
});

it('can generate a new name for a server', function () {
    $server = Server::prepareNew();
    expect($server->name)->toBe('dynamic-server-default-2');

    $server = Server::prepareNew();
    expect($server->name)->toBe('dynamic-server-default-3');
});

it('will copy the configuration of a server type to the configuration attribute', function () {
    DynamicServers::registerServerType(
        ServerType::new('big')
            ->provider('other_provider')
            ->configuration(function (Server $server) {
                return [
                    'hostname' => 'The servername: '.Str::slug($server->name),
                ];
            })
    );

    $server = Server::prepareNew('big');

    expect($server->refresh())->configuration->toBe([
        'hostname' => 'The servername: pending-server-name',
    ]);
});

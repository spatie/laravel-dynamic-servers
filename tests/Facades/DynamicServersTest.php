<?php

use Illuminate\Support\Facades\Queue;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;

beforeEach(function () {
    Queue::fake();

    $providerConfig = config('dynamic-servers.providers');
    $providerConfig['other_provider'] = ['class' => 'Dummy value'];
    config()->set('dynamic-servers.providers', $providerConfig);

    DynamicServers::registerServerType(ServerType::new('other')->provider('other_provider'));
});

it('can increase the number of servers by 1', function (string $serverType) {
    DynamicServers::increase(type: $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(1);
})->with('serverTypes');

it('can increase the number of servers by a given number', function (string $serverType) {
    DynamicServers::increase(3, $serverType);

    expect(Server::startingOrRunning()->get())->toHaveCount(3);
})->with('serverTypes');

it('can decrease the number of servers by 1', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(type: $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(2);
})->with('serverTypes');

it('can decrease the number of servers by a given number', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(2, $serverType);

    expect(Server::startingOrRunning()->get())->toHaveCount(1);
})->with('serverTypes');

it('will not throw an exception when decreasing more servers than available', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(6, $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(0);
})->with('serverTypes');

it('can ensure a given number of servers', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::ensure(3, $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(3);

    DynamicServers::ensure(5, $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(5);

    DynamicServers::ensure(2, $serverType);

    expect(Server::startingOrRunning()->type($serverType)->get())->toHaveCount(2);
})->with('serverTypes');

dataset('serverTypes', [
    'default',
    'other',
]);

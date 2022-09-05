<?php

use Illuminate\Support\Facades\Queue;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;

beforeEach(function () {
    Queue::fake();
});

it('can increase the number of servers by 1', function (string $serverType) {
    DynamicServers::increase(type: $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(1);
})->with('serverTypes');

it('can increase the number of servers by a given number', function (string $serverType) {
    DynamicServers::increase(3, $serverType);

    expect(Server::provisioned()->get())->toHaveCount(3);
})->with('serverTypes');

it('can decrease the number of servers by 1', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(type: $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(2);
})->with('serverTypes');

it('can decrease the number of servers by a given number', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(2, $serverType);

    expect(Server::provisioned()->get())->toHaveCount(1);
})->with('serverTypes');

it('will not throw an exception when decreasing more servers than available', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::decrease(6, $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(0);
})->with('serverTypes');

it('can ensure a given number of servers', function (string $serverType) {
    Server::factory()->running()->count(3)->create(['type' => $serverType]);

    DynamicServers::ensure(3, $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(3);

    DynamicServers::ensure(5, $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(5);

    DynamicServers::ensure(2, $serverType);

    expect(Server::provisioned()->type($serverType)->get())->toHaveCount(2);
})->with('serverTypes');

it('will not destroy servers of other types', function () {
    Server::factory()->running()->count(3)->create(['type' => 'default']);
    Server::factory()->running()->count(3)->create(['type' => 'other']);

    DynamicServers::ensure(1);

    expect(Server::provisioned()->type('default')->get())->toHaveCount(1);
    expect(Server::provisioned()->type('other')->get())->toHaveCount(3);

    DynamicServers::ensure(2, 'other');

    expect(Server::provisioned()->type('default')->get())->toHaveCount(1);
    expect(Server::provisioned()->type('other')->get())->toHaveCount(2);
});

it('will restart running servers', function() {
    $server = Server::factory()->running()->create();

    DynamicServers::reboot();

    expect($server->refresh()->rebootRequested())->toBeFalse();
    expect($server->refresh()->status)->toBe(ServerStatus::Rebooting);
});

it('will mark starting servers as to be restarted', function() {
    $server = Server::factory()->starting()->create();

    DynamicServers::reboot();

    expect($server->refresh()->rebootRequested())->toBeTrue();
    expect($server->refresh()->status)->toBe(ServerStatus::Starting);
});

it('will mark rebooting servers as to be rebooting again', function() {
    $server = Server::factory()->rebooting()->create();

    DynamicServers::reboot();

    expect($server->refresh()->rebootRequested())->toBeTrue();
    expect($server->refresh()->status)->toBe(ServerStatus::Rebooting);
});

dataset('serverTypes', [
    'default',
    'other',
]);

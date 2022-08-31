<?php

use Illuminate\Support\Facades\Queue;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;

beforeEach(function () {
    Queue::fake();
});

it('can increase the number of servers by 1', function () {
    DynamicServers::increase();

    expect(Server::startingOrRunning()->get())->toHaveCount(1);
});

it('can increase the number of servers by a given number', function () {
    DynamicServers::increase(3);

    expect(Server::startingOrRunning()->get())->toHaveCount(3);
});

it('can decrease the number of servers by 1', function () {
    Server::factory()->running()->count(3)->create();

    DynamicServers::decrease();

    expect(Server::startingOrRunning()->get())->toHaveCount(2);
});

it('can decrease the number of servers by a given number', function () {
    Server::factory()->running()->count(3)->create();

    DynamicServers::decrease(2);

    expect(Server::startingOrRunning()->get())->toHaveCount(1);
});

it('will not throw an exception when decreasing more servers than available', function () {
    Server::factory()->running()->count(3)->create();

    DynamicServers::decrease(6);

    expect(Server::startingOrRunning()->get())->toHaveCount(0);
});

it('can ensure a given number of servers', function () {
    Server::factory()->running()->count(3)->create();

    DynamicServers::ensure(3);

    expect(Server::startingOrRunning()->get())->toHaveCount(3);

    DynamicServers::ensure(5);

    expect(Server::startingOrRunning()->get())->toHaveCount(5);

    DynamicServers::ensure(2);

    expect(Server::startingOrRunning()->get())->toHaveCount(2);
});

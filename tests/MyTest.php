<?php

use Spatie\DynamicServers\Models\Server;

it('can work with servers', function () {

    /** @var Server $server */
    $server = Server::create([
        'name' => 'my new server',
        'provider' => 'up_cloud',
    ]);

    $server->start();

    $server = $server->refresh();

    dd($server);
})->skip('do not provision real servers now');

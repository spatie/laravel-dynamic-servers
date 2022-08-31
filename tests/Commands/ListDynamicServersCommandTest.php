<?php

use function Pest\Laravel\artisan;
use Spatie\DynamicServers\Commands\ListDynamicServersCommand;
use Spatie\DynamicServers\Models\Server;

it('has a command to list all servers', function () {
    Server::factory()->create();

    artisan(ListDynamicServersCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('Name');
});

it('works when there are no servers defined', function () {
    artisan(ListDynamicServersCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('No dynamic servers found');
});

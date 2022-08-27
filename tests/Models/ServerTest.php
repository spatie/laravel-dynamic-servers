<?php

use Spatie\DynamicServers\Models\Server;

beforeEach(function() {
    /** @var Server server */
    $this->server = Server::factory()->create();
});

it('can add meta data to a server', function(mixed $value) {
    $this->server->addMeta('key', $value);

    expect($this->server->refresh()->meta['key'])->toEqual($value);
})->with([
    'a string',
    ['an', 'array', 'of', 'string'],
    true,
    1,
]);

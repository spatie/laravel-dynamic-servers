<?php

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

beforeEach(function () {
    /** @var Server server */
    $this->server = Server::factory()->create();
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

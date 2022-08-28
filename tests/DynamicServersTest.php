<?php

it('adds a macro to the config repository', function () {
    expect(config()->dynamicServerJobClass('create_server'))->toBe(\Spatie\DynamicServers\Jobs\CreateServerJob::class);
});

it('throws when adding an invalid job class', function () {
    $this->expectException(\Spatie\DynamicServers\Exceptions\JobDoesNotExist::class);

    config()->dynamicServerJobClass('something-wrong');
});

<?php

use Illuminate\Support\Arr;
use Spatie\DynamicServers\Tests\TestSupport\TestCase;

uses(TestCase::class)->in(__DIR__);

function getStub(string $name, array $overrides = []): array
{
    $properties = json_decode(file_get_contents(__DIR__."/TestSupport/stubs/{$name}.json"), true);

    foreach ($overrides as $key => $value) {
        Arr::set($properties, $key, $value);
    }

    return $properties;
}

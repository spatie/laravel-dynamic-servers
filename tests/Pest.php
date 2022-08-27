<?php

use Spatie\DynamicServers\Tests\TestSupport\TestCase;

uses(TestCase::class)->in(__DIR__);

function getStub(string $name): array
{
    return json_decode(file_get_contents(__DIR__."/stubs/{$name}.json"), true);
}

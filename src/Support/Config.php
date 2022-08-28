<?php

namespace Spatie\DynamicServers\Support;

use Illuminate\Support\Arr;
use Spatie\DynamicServers\Exceptions\JobDoesNotExist;

class Config
{
    public static function providerOption(string $providerName, string $key = null): mixed
    {
        $providerOptions = config("dynamic-servers.providers.{$providerName}.options");

        return is_null($key)
            ? $providerOptions
            : Arr::get($providerOptions, $key);
    }
}

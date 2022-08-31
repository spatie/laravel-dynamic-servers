<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Support\Config;

class ProviderDoesNotExist extends Exception
{
    public static function make(string $providerName): self
    {
        $availableNames = collect(Config::providerNames())
            ->map(function(string $name) {
                return "`{$name}`";
            })
            ->join(', ', ' and ');

        return new static("There is no provider registered with name `{$providerName}`. Available providers are: {$availableNames}. You can register providers in the `providers` key of the `dynamic_servers` config file.");
    }
}

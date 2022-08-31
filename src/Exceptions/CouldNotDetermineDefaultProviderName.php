<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;

class CouldNotDetermineDefaultProviderName extends Exception
{
    public static function make(): self
    {
        return new static('Could not determine a default provider name. Make sure the `dynamic-servers` config file has a `providers` key with at least one provider in it.');
    }
}

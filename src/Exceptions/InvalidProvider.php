<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Models\Server;

class InvalidProvider extends Exception
{
    public static function make(Server $server)
    {
        return new static("Server id {$server->id} has an invalid provider `{$server->provider}`. Make sure you have configured a provider with that name in the config file.");
    }
}

<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Models\Server;

class CannotStopServer extends Exception
{
    public static function wrongStatus(Server $server): self
    {
        return new static("Could not start server id {$server->id} because it has status `{$server->status->value}`");
    }
}

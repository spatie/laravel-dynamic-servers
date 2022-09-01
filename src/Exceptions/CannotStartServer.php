<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Models\Server;

class CannotStartServer extends Exception
{
    public static function wrongStatus(Server $server): self
    {
        return new self("Could not start server id {$server->id} because it has status `{$server->status->value}`");
    }
}

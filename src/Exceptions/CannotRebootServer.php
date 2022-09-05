<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Models\Server;

class CannotRebootServer extends Exception
{
    public static function wrongStatus(Server $server): self
    {
        return new self("Could not reboot server id {$server->id} because it has status `{$server->status->value}`. Only running servers can be rebooted");
    }
}

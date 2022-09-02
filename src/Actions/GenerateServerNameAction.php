<?php

namespace Spatie\DynamicServers\Actions;

use Spatie\DynamicServers\Models\Server;

class GenerateServerNameAction
{
    public function execute(Server $server): string
    {
        return "dynamic-server-{$server->type}-{$server->id}";
    }
}

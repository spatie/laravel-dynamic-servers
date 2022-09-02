<?php

namespace Spatie\DynamicServers\Actions;

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerLimitHitEvent;
use Spatie\DynamicServers\Exceptions\CannotStartServer;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class GenerateServerNameAction
{
    public function execute(Server $server): string
    {
        return "dynamic-server-{$server->type}-{$server->id}";
    }
}

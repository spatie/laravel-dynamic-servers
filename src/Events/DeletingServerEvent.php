<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class DeletingServerEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

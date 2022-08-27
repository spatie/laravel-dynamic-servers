<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class ServerDeletedEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

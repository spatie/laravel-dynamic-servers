<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class ServerStoppedEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class ServerErroredEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

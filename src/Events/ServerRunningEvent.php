<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class ServerRunningEvent
{
    public function __construct(
        public Server $server
    ) {
    }
}

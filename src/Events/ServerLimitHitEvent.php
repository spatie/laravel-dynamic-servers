<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class ServerLimitHitEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

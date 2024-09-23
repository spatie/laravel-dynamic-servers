<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class RebootingServerEvent
{
    public function __construct(
        public Server $server,
    ) {}
}

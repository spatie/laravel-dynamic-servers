<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class StoppingServerEvent
{
    public function __construct(
        public Server $server
    ) {}
}

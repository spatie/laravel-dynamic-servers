<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Models\Server;

class CreatingServerEvent
{
    public function __construct(
        public Server $server,
    )
    {}

}

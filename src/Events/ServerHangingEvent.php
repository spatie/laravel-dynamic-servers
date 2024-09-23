<?php

namespace Spatie\DynamicServers\Events;

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class ServerHangingEvent
{
    public function __construct(
        public Server $server,
        public ServerStatus $previousStatus,
    ) {}
}

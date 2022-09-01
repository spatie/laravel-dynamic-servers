<?php

namespace Spatie\DynamicServers\ServerProviders;

use Spatie\DynamicServers\Models\Server;

abstract class ServerProvider
{
    protected Server $server;

    public function setServer(Server $server)
    {
        $this->server = $server;
    }

    abstract public function createServer(): void;

    abstract public function hasStarted(): bool;

    abstract public function stopServer(): void;

    abstract public function hasStopped(): bool;

    abstract public function deleteServer(): void;

    abstract public function hasBeenDeleted(): bool;

    abstract public function currentServerCount(): int;
}

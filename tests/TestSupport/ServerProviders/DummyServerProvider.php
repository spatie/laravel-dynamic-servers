<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

use Spatie\DynamicServers\ServerProviders\ServerProvider;

class DummyServerProvider extends ServerProvider
{
    public function createServer(): void
    {
        //
    }

    public function hasStarted(): bool
    {
        return true;
    }

    public function stopServer(): void
    {
        //
    }

    public function hasStopped(): bool
    {
        return true;
    }

    public function deleteServer(): void
    {
    }

    public function hasBeenDeleted(): bool
    {
        return true;
    }

    public function currentServerCount(): int
    {
        return 0;
    }
}

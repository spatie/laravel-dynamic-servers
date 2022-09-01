<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

class EndlessStopServerProvider extends DummyServerProvider
{
    public function hasStopped(): bool
    {
        return false;
    }
}

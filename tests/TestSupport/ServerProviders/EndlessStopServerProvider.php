<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

use Spatie\DynamicServers\ServerProviders\ServerProvider;

class EndlessStopServerProvider extends DummyServerProvider
{
    public function hasStopped(): bool
    {
        return false;
    }
}

<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

use Spatie\DynamicServers\ServerProviders\ServerProvider;

class EndlessStartServerProvider extends DummyServerProvider
{
    public function hasStarted(): bool
    {
        return false;
    }
}

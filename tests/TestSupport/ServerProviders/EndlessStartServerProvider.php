<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

class EndlessStartServerProvider extends DummyServerProvider
{
    public function hasStarted(): bool
    {
        return false;
    }
}

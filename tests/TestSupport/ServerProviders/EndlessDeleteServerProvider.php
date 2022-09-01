<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

class EndlessDeleteServerProvider extends DummyServerProvider
{
    public function hasBeenDeleted(): bool
    {
        return false;
    }
}

<?php

namespace Spatie\DynamicServers\Tests\TestSupport\ServerProviders;

use Spatie\DynamicServers\ServerProviders\ServerProvider;

class EndlessDeleteServerProvider extends DummyServerProvider
{
    public function hasBeenDeleted(): bool
    {
        return false;
    }
}

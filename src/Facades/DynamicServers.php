<?php

namespace Spatie\DynamicServers\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\DynamicServers\Support\DynamicServersManager;

/**
 * @see DynamicServersManager
 */
class DynamicServers extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dynamicServers';
    }
}

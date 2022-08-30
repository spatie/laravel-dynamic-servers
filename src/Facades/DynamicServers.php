<?php

namespace Spatie\DynamicServers\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\DynamicServers\Support\DynamicServers as DynamicServersClass;

/**
 * @see DynamicServersClass
 */
class DynamicServers extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dynamicServers';
    }
}

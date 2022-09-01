<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;

class InvalidAction extends Exception
{
    public static function make(string $actionName, ?string $actionClass, Exception $exception)
    {
        return new self("Could not instanciate action class `{$actionClass}` for action `{$actionName}` because: {$exception->getMessage()}", previous: $exception);
    }
}

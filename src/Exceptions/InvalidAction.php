<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;

class InvalidAction extends Exception
{
    public static function doesNotExist(string $actionName)
    {
        return new self("Did not find action `$actionName`. Make sure it is defined in the `actions` key of the `dynamic-servers` config file");
    }

    public static function couldNotMake(string $actionName, ?string $actionClass, Exception $exception)
    {
        return new self("Could not instanciate action class `{$actionClass}` for action `{$actionName}` because: {$exception->getMessage()}", previous: $exception);
    }


}

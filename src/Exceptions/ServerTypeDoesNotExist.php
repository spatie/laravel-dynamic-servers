<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;
use Spatie\DynamicServers\Facades\DynamicServers;

class ServerTypeDoesNotExist extends Exception
{
    public static function make(string $serverTypeName): self
    {
        $availableNames = collect(DynamicServers::serverTypeNames())
            ->map(function (string $name) {
                return "`{$name}`";
            })
            ->join(', ', ' and ');

        return new self("There is no server type registered with name `{$serverTypeName}`. Available names are: {$availableNames}. You can register more using `DynamicServers::registerServerType()");
    }
}

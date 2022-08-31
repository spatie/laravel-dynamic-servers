<?php

namespace Spatie\DynamicServers\ServerProviders\DigitalOcean\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;
use Spatie\DynamicServers\Models\Server;

class CannotGetDigitalOceanServerDetails extends Exception
{
    public static function make(
        Server $server,
        Response $response): self
    {
        $reason = $response->json('message');

        return new static("Could refresh details for DigitalOcean server id {$server->id}: $reason");
    }
}

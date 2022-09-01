<?php

namespace Spatie\DynamicServers\ServerProviders\UpCloud\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;
use Spatie\DynamicServers\Models\Server;

class CannotGetUpCloudServerDetails extends Exception
{
    public static function make(
        Server $server,
        Response $response): self
    {
        $reason = $response->json('error.error_message');

        return new self("Could refresh details for UpCloud server id {$server->id}: $reason");
    }
}

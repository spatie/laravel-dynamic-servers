<?php

namespace Spatie\DynamicServers\Actions;

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Exceptions\CannotStartServer;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class StartServerAction
{
    public function execute(Server $server): void
    {
        if ($server->status !== ServerStatus::New) {
            throw CannotStartServer::wrongStatus($server);
        }

        /** @var class-string<CreateServerJob> $createServerJobClass */
        $createServerJobClass = Config::dynamicServerJobClass('create_server');

        dispatch(new $createServerJobClass($server));

        $server->markAs(ServerStatus::Starting);
    }
}

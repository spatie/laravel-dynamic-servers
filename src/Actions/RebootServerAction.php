<?php

namespace Spatie\DynamicServers\Actions;

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Exceptions\CannotRebootServer;
use Spatie\DynamicServers\Jobs\RebootServerJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class RebootServerAction
{
    public function execute(Server $server): void
    {
        if ($server->status !== ServerStatus::Running) {
            throw CannotRebootServer::wrongStatus($server);
        }

        /** @var class-string<RebootServerJob> $rebootServerJobClass */
        $rebootServerJobClass = Config::dynamicServerJobClass('reboot_server');

        dispatch(new $rebootServerJobClass($server));

        $server->update([
            'reboot_requested_at' => null,
        ]);

        $server->markAs(ServerStatus::Rebooting);
    }
}

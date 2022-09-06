<?php

namespace Spatie\DynamicServers\Actions;

use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerLimitHitEvent;
use Spatie\DynamicServers\Exceptions\CannotStartServer;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class StartServerAction
{
    public function execute(Server $server): void
    {
        if (! $this->allowedToStartServer($server)) {
            $server->delete();

            return;
        }

        if ($server->status !== ServerStatus::New) {
            throw CannotStartServer::wrongStatus($server);
        }

        /** @var class-string<CreateServerJob> $createServerJobClass */
        $createServerJobClass = Config::dynamicServerJobClass('create_server');

        dispatch(new $createServerJobClass($server));

        $server->markAs(ServerStatus::Starting);
    }

    protected function allowedToStartServer(Server $server): bool
    {
        $amountOfServers = $this->getCurrentServerCount($server);

        $limit = config("dynamic-servers.providers.{$server->provider}.maximum_servers_in_account", 20);

        if ($amountOfServers < $limit) {
            return true;
        }

        event(new ServerLimitHitEvent($server));

        if (config('dynamic-servers.throw_exception_when_hitting_maximum_server_limit')) {
            throw CannotStartServer::limitHit();
        }
    }

    protected function getCurrentServerCount(Server $server): int
    {
        $cacheKey = "server-provider-{$server->provider}-server-count";

        cache()->remember("server-provider-{$server->provider}-server-count", 60, function () use ($server) {
            return $server->serverProvider()->currentServerCount();
        });

        return cache()->increment($cacheKey);
    }
}

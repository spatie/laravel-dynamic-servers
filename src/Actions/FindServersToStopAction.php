<?php

namespace Spatie\DynamicServers\Actions;

use Illuminate\Support\Collection;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class FindServersToStopAction
{
    /**
     * @return Collection<Server>
     */
    public function execute(int $numberOfServersToStop, string $type): Collection
    {
        return Server::query()
            ->where('status', ServerStatus::Running)
            ->where('type', $type)
            ->status(ServerStatus::Running)
            ->limit($numberOfServersToStop)
            ->get();
    }
}

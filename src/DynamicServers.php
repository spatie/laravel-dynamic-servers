<?php

namespace Spatie\DynamicServers;

use Closure;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class DynamicServers
{
    protected ?Closure $determineServerCountUsing;

    /**
     * @param \Closure $determineServerCountUsing
     *
     * @return void
     */
    public function determineServerCount(Closure $determineServerCountUsing)
    {
        $this->determineServerCountUsing = $determineServerCountUsing;
    }

    public function ensure(int $count): self
    {


        return $this;
    }

    public function increaseCount(int $count)
    {
        foreach (range(1, $count) as $i) {
            Server::create([
                'name' => 'automatically created server',
                'provider' => 'up_cloud',
            ]);
        }
    }

    public function decreaseCount(int $by): self
    {
        Server::query()
            ->where('status', ServerStatus::Running)
            ->limit($by)
            ->each(fn(Server $server) => $server->delete());

        return $this;
    }
}

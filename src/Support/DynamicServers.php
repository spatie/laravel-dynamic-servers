<?php

namespace Spatie\DynamicServers\Support;

use Closure;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class DynamicServers
{
    protected ?Closure $determineServerCountUsing = null;

    /**
     * @param  Closure(DynamicServers): void  $determineServerCountUsing
     * @return void
     */
    public function determineServerCount(Closure $determineServerCountUsing): self
    {
        $this->determineServerCountUsing = $determineServerCountUsing;

        return $this;
    }

    public function monitor(): self
    {
        if (is_null($this->determineServerCountUsing)) {
            return $this;
        }

        ($this->determineServerCountUsing)($this);
    }

    public function ensure(int $desiredCount): self
    {
        $startingAndRunningServerCount = Server::query()
            ->status(ServerStatus::Starting, ServerStatus::Running)
            ->count();

        if ($startingAndRunningServerCount < $desiredCount) {
            $extraServersNeeded = $desiredCount - $startingAndRunningServerCount;

            $this->increaseCount($extraServersNeeded);

            return $this;
        }

        if ($startingAndRunningServerCount > $desiredCount) {
            $lessServersNeeded = $startingAndRunningServerCount - $desiredCount;

            $this->decreaseCount($lessServersNeeded);

            return $this;
        }

        return $this;
    }

    public function increaseCount(int $count = 1): self
    {
        foreach (range(1, $count) as $i) {
            Server::create([
                'name' => 'automatically created server',
                'provider' => 'up_cloud',
            ])->start();
        }

        return $this;
    }

    public function increase(int $by = 1): self
    {
        return $this->increaseCount($by);
    }

    public function decreaseCount(int $by = 1): self
    {
        Server::query()
            ->where('status', ServerStatus::Running)
            ->limit($by)
            ->get()
            ->each(function (Server $server) {
                return $server->stop();
            });

        return $this;
    }

    public function decrease(int $by = 1): self
    {
        return $this->decreaseCount($by);
    }
}

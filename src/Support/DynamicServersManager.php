<?php

namespace Spatie\DynamicServers\Support;

use Closure;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use Spatie\DynamicServers\Support\ServerTypes\ServerTypes;
use Spatie\DynamicServers\Actions\FindServersToStopAction;

class DynamicServersManager
{
    protected ?Closure $determineServerCountUsing = null;

    /**
     * @param  Closure(DynamicServersManager): void  $determineServerCountUsing
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

        return $this;
    }

    public function ensure(int $desiredCount, string $type = 'default'): self
    {
        $startingAndRunningServerCount = Server::query()
            ->where('type', $type)
            ->provisioned()
            ->count();

        if ($startingAndRunningServerCount < $desiredCount) {
            $extraServersNeeded = $desiredCount - $startingAndRunningServerCount;

            $this->increaseCount($extraServersNeeded, $type);

            return $this;
        }

        if ($startingAndRunningServerCount > $desiredCount) {
            $lessServersNeeded = $startingAndRunningServerCount - $desiredCount;

            $this->decreaseCount($lessServersNeeded, $type);

            return $this;
        }

        return $this;
    }

    public function increaseCount(int $count = 1, string $type = 'default'): self
    {
        foreach (range(1, $count) as $i) {
            Server::prepareNew($type)->start();
        }

        return $this;
    }

    public function increase(int $by = 1, string $type = 'default'): self
    {
        return $this->increaseCount($by, $type);
    }

    public function decreaseCount(int $by = 1, string $type = 'default'): self
    {
        /** @var FindServersToStopAction $findServersToStopAction */
        $findServersToStopAction = Config::action('find_servers_to_stop');

        $servers = $findServersToStopAction->execute($by, $type);

        $servers->each(fn (Server $server) => $server->stop());

        return $this;
    }

    public function decrease(int $by = 1, string $type = 'default'): self
    {
        return $this->decreaseCount($by, $type);
    }

    public function getServerType(string $serverType): ServerType
    {
        return app(ServerTypes::class)->find($serverType);
    }

    public function registerServerType(ServerType $serverType): self
    {
        app(ServerTypes::class)->register($serverType);

        return $this;
    }

    public function serverTypeNames(): array
    {
        return app(ServerTypes::class)->allNames();
    }
}

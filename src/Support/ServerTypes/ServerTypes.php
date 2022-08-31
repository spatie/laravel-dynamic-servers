<?php

namespace Spatie\DynamicServers\Support\ServerTypes;

use Illuminate\Support\Collection;
use Spatie\DynamicServers\Exceptions\ServerTypeDoesNotExist;

class ServerTypes
{
    /** @var Collection<ServerType> */
    protected Collection $serverTypes;

    public function __construct() {
        $this->serverTypes = collect();
    }

    public function register(ServerType $serverType): self
    {
        $this->serverTypes->put($serverType->name, $serverType);

        return $this;
    }

    public function find(string $serverTypeName): ServerType
    {
        if (! $this->serverTypes->has($serverTypeName)) {
            throw ServerTypeDoesNotExist::make($serverTypeName);
        }

        return $this->serverTypes->get($serverTypeName);
    }

    public function allNames(): array
    {
        return $this->serverTypes->keys()->toArray();
    }
}

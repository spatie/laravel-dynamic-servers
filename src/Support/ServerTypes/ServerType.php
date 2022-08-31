<?php

namespace Spatie\DynamicServers\Support\ServerTypes;

use Closure;
use Spatie\DynamicServers\Exceptions\ProviderDoesNotExist;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class ServerType
{
    public string $name;
    public string $providerName;
    public array|Closure $configuration;

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function __construct(
        string $name,
        string $providerName = null,
        string $configuration = null
    )
    {
        $this->name = $name;

        $this->providerName = $providerName ?? Config::defaultProviderName();

        $this->configuration = $configuration ?? [];
    }

    public function provider(string $providerName): self
    {
        if (! in_array($providerName, Config::providerNames())) {
            throw ProviderDoesNotExist::make($providerName);
        }

        $this->providerName = $providerName;

        return $this;
    }

    public function configuration(array|Closure $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getConfiguration(Server $server): array
    {
        $configuration = $this->configuration;

        if (is_callable($configuration)) {
            $configuration = $configuration($server);
        }

        return $configuration;
    }
}

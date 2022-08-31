<?php

namespace Spatie\DynamicServers\ServerProviders\DigitalOcean;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\DynamicServers\ServerProviders\ServerProvider;
use Spatie\DynamicServers\ServerProviders\DigitalOcean\Exceptions\CannotGetDigitalOceanServerDetails;

class DigitalOcean extends ServerProvider
{
    public function createServer(): void
    {
        $response = $this->request()
            ->post('/droplets', [
                'name' => $this->server->name,
                'region' => $this->server->option('region'),
                'size' => $this->server->option('size'),
                'image' => $this->server->option('image'),
                'vpc_uuid' => $this->server->option('vpc_uuid'),
            ]);

        if (! $response->successful()) {
            throw new Exception($response->json('message'));
        }

        $digitalOceanServer = DigitalOceanServer::fromApiPayload($response->json('droplet'));

        $this->server->addMeta('server_properties', $digitalOceanServer->toArray());
    }

    public function hasStarted(): bool
    {
        $digitalOceanServer = $this->getServer();

        return $digitalOceanServer->status === DigitalOceanServerStatus::Active;
    }

    public function stopServer(): void
    {
        $serverId = $this->server->meta('server_properties.id');

        $response = $this->request()->post("/droplets/{$serverId}/actions", [
            'type' => 'shutdown',
        ]);

        if (! $response->successful()) {
            throw new Exception($response->json('message'));
        }
    }

    public function hasBeenStopped(): bool
    {
        $digitalOceanServer = $this->getServer();

        return $digitalOceanServer->status === DigitalOceanServerStatus::Off;
    }

    public function deleteServer(): void
    {
        $serverId = $this->server->meta('server_properties.id');

        $response = $this->request()
            ->delete("/droplets/{$serverId}");

        if (! $response->successful()) {
            throw new Exception($response->json('message', 'Could not delete server'));
        }
    }

    public function hasBeenDeleted(): bool
    {
        // to do: implement

        return true;
    }

    public function getServer(): DigitalOceanServer
    {
        $serverId = $this->server->meta('server_properties.id');

        $response = $this->request()->get("/droplets/{$serverId}");

        if (! $response->successful()) {
            throw CannotGetDigitalOceanServerDetails::make($this->server, $response);
        }

        return DigitalOceanServer::fromApiPayload($response->json('droplet'));
    }

    protected function request(): PendingRequest
    {
        return Http::withToken(
            $this->server->option('token')
        )->baseUrl('https://api.digitalocean.com/v2');
    }
}

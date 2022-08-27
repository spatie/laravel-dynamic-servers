<?php

namespace Spatie\DynamicServers\ServerProviders\UpCloud;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\DynamicServers\ServerProviders\ServerProvider;
use Spatie\DynamicServers\ServerProviders\UpCloud\Exceptions\CannotGetUpCloudServerDetails;

class UpCloud extends ServerProvider
{
    public function createServer(): void
    {
        $response = $this->request()
            ->post('/server', [
                'server' => [
                    'zone' => 'de-fra1',
                    'title' => $this->server->name,
                    'hostname' => Str::slug($this->server->name),
                    'plan' => '2xCPU-4GB',
                    'storage_devices' => [
                        'storage_device' => [
                            [
                                'action' => 'clone',
                                'storage' => $this->server->option('disk_image'),
                                'title' => Str::slug($this->server->name).'-disk',
                                'tier' => 'maxiops',
                            ],
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new Exception($response->json('error.error_message'));
        }

        $upCloudServer = UpCloudServer::fromApiPayload($response->json('server'));

        $this->server->addMeta('server_properties', $upCloudServer->toArray());
    }

    public function hasStarted(): bool
    {
        $upCloudServer = $this->getServer();

        return $upCloudServer->status === UpCloudServerStatus::Started;
    }

    public function stopServer(): void
    {
        $serverUuid = $this->server->meta('server_properties.uuid');

        $response = $this->request()->post("/server/{$serverUuid}/stop", [
            'stop_server' => [
                'stop_type' => 'soft',
                'timeout' => 60,
            ],
        ]);

        if (! $response->successful()) {
            throw new Exception($response->json('error.error_message'));
        }
    }

    public function hasBeenStopped(): bool
    {
        $upCloudServer = $this->getServer();

        return $upCloudServer->status === UpCloudServerStatus::Stopped;
    }

    public function deleteServer(): void
    {
        $serverUuid = $this->server->meta('server_properties.uuid');

        $response = $this->request()
            ->delete("/server/{$serverUuid}?storages=1&backups=delete");

        if ($response->status() !== 204) {
            throw new Exception($response->json('error.error_message', 'Could not delete server'));
        }
    }

    public function hasBeenDeleted(): bool
    {
        // todo: implement

        return true;
    }

    public function getServer(): UpCloudServer
    {
        $serverUuid = $this->server->meta('server_properties.uuid');

        $response = $this->request()->get("/server/{$serverUuid}");

        if (! $response->successful()) {
            throw CannotGetUpCloudServerDetails::make($this->server, $response);
        }

        return UpCloudServer::fromApiPayload($response->json('server'));
    }

    protected function request(): PendingRequest
    {
        return Http::withBasicAuth(
            $this->server->option('username'),
            $this->server->option('password')
        )->baseUrl('https://api.upcloud.com/1.3');
    }
}

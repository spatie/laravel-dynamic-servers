<?php

namespace Spatie\DynamicServers\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Exceptions\CannotStartServer;
use Spatie\DynamicServers\Exceptions\CannotStopServer;
use Spatie\DynamicServers\Exceptions\InvalidProvider;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Jobs\StopServerJob;
use Spatie\DynamicServers\ServerProviders\ServerProvider;
use Spatie\DynamicServers\Support\Config;

class Server extends Model
{
    use HasFactory;

    public $guarded = [];

    public $table = 'dynamic_servers';

    public $casts = [
        'status_updated_at' => 'datetime',
        'status' => ServerStatus::class,
        'meta' => AsArrayObject::class,
    ];

    public static function booted()
    {
        Server::creating(function (Server $server) {
            if (is_null($server->status)) {
                $server->status = ServerStatus::New;
            }

            if (empty($server->meta)) {
                $server->meta = [];
            }
        });
    }

    public function start(): self
    {
        info('starting server');
        if ($this->status !== ServerStatus::New) {
            throw CannotStartServer::wrongStatus($this);
        }

        /** @var class-string<CreateServerJob> $createServerJobClass */
        $createServerJobClass = Config::dynamicServerJobClass('create_server');

        info('dispatching create server');
        dispatch(new $createServerJobClass($this));

        $this->markAs(ServerStatus::Starting);

        return $this;
    }

    public function stop(): self
    {
        if ($this->status !== ServerStatus::Running) {
            throw CannotStopServer::wrongStatus($this);
        }

        /** @var class-string<StopServerJob> $stopServerJobClass */
        $stopServerJobClass = Config::dynamicServerJobClass('stop_server');

        dispatch(new $stopServerJobClass($this));

        $this->markAs(ServerStatus::Stopping);

        return $this;
    }

    public function markAs(ServerStatus $status): self
    {
        $this->update([
            'status' => $status,
            'status_updated_at' => now(),
        ]);

        return $this;
    }

    public function provider(): ServerProvider
    {
        /** @var class-string<ServerProvider> $providerClassName */
        $providerClassName = config("dynamic-servers.providers.{$this->provider}.class") ?? '';

        if (! is_a($providerClassName, ServerProvider::class, true)) {
            throw InvalidProvider::make($this);
        }

        /** @var ServerProvider $providerClass */
        $serverProvider = app($providerClassName);

        $serverProvider->setServer($this);

        return $serverProvider;
    }

    public function markAsErrored(Exception $exception)
    {
        $this->update([
            'status' => ServerStatus::Errored,
            'status_updated_at' => now(),
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'exception_trace' => $exception->getTraceAsString(),
        ]);
    }

    public function meta(string $key, mixed $default = null)
    {
        return Arr::get($this->meta, $key) ?? $default;
    }

    public function addMeta(string $name, string|array|int|bool $value): self
    {
        $this->meta[$name] = $value;

        $this->save();

        return $this;
    }

    public function option(string $key): mixed
    {
        return Config::providerOption($this->provider, $key);
    }



    public function scopeStatus(Builder $query, ServerStatus ...$statuses): void
    {
        $query->whereIn('status', $statuses);
    }
}

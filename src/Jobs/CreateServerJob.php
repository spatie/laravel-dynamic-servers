<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Events\CreatingServerEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class CreateServerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
    }

    public function handle()
    {
        try {
            $this->server->provider()->createServer();
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);

            return;
        }

        event(new CreatingServerEvent($this->server));

        $verifyServerStartedJob = Config::jobClass('verify_server_started');

        dispatch(new $verifyServerStartedJob($this->server));
    }
}

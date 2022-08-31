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

class CreateServerJob extends DynamicServerJob
{
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

        /** @var class-string<VerifyServerStartedJob> $verifyServerStartedJob */
        $verifyServerStartedJob = Config::dynamicServerJobClass('verify_server_started');

        dispatch(new $verifyServerStartedJob($this->server));
    }
}

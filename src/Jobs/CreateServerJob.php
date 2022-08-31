<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Events\CreatingServerEvent;
use Spatie\DynamicServers\Support\Config;

class CreateServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->serverProvider()->createServer();
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

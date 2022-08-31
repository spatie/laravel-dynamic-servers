<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Events\StoppingServerEvent;
use Spatie\DynamicServers\Support\Config;

class StopServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->serverProvider()->stopServer();
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);

            return;
        }

        event(new StoppingServerEvent($this->server));

        /** @var class-string<VerifyServerStoppedJob> $verifyServerStoppedJob */
        $verifyServerStoppedJob = Config::dynamicServerJobClass('verify_server_stopped');

        dispatch(new $verifyServerStoppedJob($this->server));
    }
}

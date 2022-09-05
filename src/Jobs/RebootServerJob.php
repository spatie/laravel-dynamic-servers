<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Events\RebootingServerEvent;
use Spatie\DynamicServers\Support\Config;

class RebootServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->serverProvider()->rebootServer();
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);

            return;
        }

        event(new RebootingServerEvent($this->server));

        /** @var class-string<VerifyServerRebootedJob> $verifyServerDeletedJob */
        $verifyServerRebootedJob = Config::dynamicServerJobClass('verify_server_rebooted');

        dispatch(new $verifyServerRebootedJob($this->server));
    }
}

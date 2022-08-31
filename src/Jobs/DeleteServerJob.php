<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\DeletingServerEvent;
use Spatie\DynamicServers\Support\Config;

class DeleteServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->serverProvider()->deleteServer();

            $this->server->markAs(ServerStatus::Deleting);
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);

            return;
        }

        event(new DeletingServerEvent($this->server));

        /** @var class-string<VerifyServerDeletedJob> $verifyServerDeletedJob */
        $verifyServerDeletedJob = Config::dynamicServerJobClass('verify_server_deleted');

        dispatch(new $verifyServerDeletedJob($this->server));
    }
}

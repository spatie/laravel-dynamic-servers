<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\DeletingServerEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class DeleteServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->provider()->deleteServer();

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

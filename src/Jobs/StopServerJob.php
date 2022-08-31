<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Events\StoppingServerEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class StopServerJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            $this->server->provider()->stopServer();
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

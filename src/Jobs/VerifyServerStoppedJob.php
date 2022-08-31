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
use Spatie\DynamicServers\Events\ServerStoppedEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class VerifyServerStoppedJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            if ($this->server->provider()->hasBeenStopped()) {
                $this->server->markAs(ServerStatus::Stopped);
                event(new ServerStoppedEvent($this->server));

                /** @var class-string<DeleteServerJob> $deleteServerJob */
                $deleteServerJob = Config::dynamicServerJobClass('delete_server');

                dispatch(new $deleteServerJob($this->server));

                return;
            }

            $this->release(20);
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);
        }
    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}

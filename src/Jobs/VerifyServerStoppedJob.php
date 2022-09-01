<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerStoppedEvent;
use Spatie\DynamicServers\Support\Config;

class VerifyServerStoppedJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            if($this->server->isNotResponding()) {
                $this->server->markAsHanging();

                return;
            }

            if ($this->server->serverProvider()->hasBeenStopped()) {
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

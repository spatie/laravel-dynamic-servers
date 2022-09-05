<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerRunningEvent;

class VerifyServerRebootedJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            if ($this->server->isProbablyHanging()) {
                $this->server->markAsHanging();

                return;
            }

            if ($this->server->serverProvider()->hasStarted()) {
                $previousStatus = $this->server->status;

                $this->server->markAs(ServerStatus::Running);

                event(new ServerRunningEvent($this->server, $previousStatus));

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

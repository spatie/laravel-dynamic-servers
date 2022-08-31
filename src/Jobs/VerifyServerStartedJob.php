<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Events\ServerRunningEvent;

class VerifyServerStartedJob extends DynamicServerJob
{
    public function handle()
    {
        try {
            if ($this->server->provider()->hasStarted()) {
                $this->server->markAs(ServerStatus::Running);

                event(new ServerRunningEvent($this->server));

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

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
use Spatie\DynamicServers\Events\ServerRunningEvent;
use Spatie\DynamicServers\Models\Server;

class VerifyServerStartedJob  extends DynamicServerJob
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

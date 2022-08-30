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

class VerifyServerStartedJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
    }

    public function handle()
    {
        info('In verify server started');
        try {
            if ($this->server->provider()->hasStarted()) {
                info('running');
                $this->server->markAs(ServerStatus::Running);

                event(new ServerRunningEvent($this->server));

                return;
            }

            info('releasing because not started');
            $this->release(10);
        } catch (Exception $exception) {
            info('erroring');
            $this->server->markAsErrored($exception);

            report($exception);
        }
    }
}

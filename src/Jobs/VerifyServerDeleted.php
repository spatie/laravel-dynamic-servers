<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Spatie\DynamicServers\Actions\MarkServerAsReadyAction;
use Spatie\DynamicServers\Events\ServerDeletedEvent;
use Spatie\DynamicServers\Events\ServerRunningEvent;
use Spatie\DynamicServers\Events\ServerStoppedEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\UpCloud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Enums\ServerStatus;

class VerifyServerDeleted implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
    }

    public function handle(): bool
    {
        try {
            if ($this->server->provider()->hasBeenDeleted()) {

                $this->server->markAs(ServerStatus::Deleted);

                event(new ServerDeletedEvent($this->server));

                return;
            }

            $this->release(60);
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);
        }
    }
}

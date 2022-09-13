<?php

namespace Spatie\DynamicServers\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Models\Server;

abstract class DynamicServerJob implements ShouldQueue, ShouldBeUnique
{
    public $uniqueFor = 3600;

    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
        $this->onQueue(config('dynamic-servers.queue'));
    }

    public function uniqueId()
    {
        return $this->server->id;
    }
}

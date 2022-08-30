<?php

namespace Spatie\DynamicServers\Commands;

use Illuminate\Console\Command;
use Spatie\DynamicServers\Facades\DynamicServers;

class MonitorDynamicServersCommand extends Command
{
    public $signature = 'dynamic-servers:monitor';

    public function handle()
    {
        DynamicServers::monitor();

        $this->info('All done...');
    }
}

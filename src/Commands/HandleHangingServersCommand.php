<?php

namespace Spatie\DynamicServers\Commands;

use Illuminate\Console\Command;

class HandleHangingServersCommand extends Command
{
    public $signature = 'dynamic-servers:hanging';

    public function handle()
    {
        $this->info('Detecting hanging servers...');

        // TODO: implement

        return self::SUCCESS;
    }
}

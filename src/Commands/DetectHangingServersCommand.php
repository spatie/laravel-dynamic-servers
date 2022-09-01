<?php

namespace Spatie\DynamicServers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class DetectHangingServersCommand extends Command
{
    public $signature = 'dynamic-servers:hanging';

    public function handle()
    {
        $this->info('Detecting hanging servers...');

        $thresholdInMinutes = config('dynamic-servers.mark_server_as_hanging_after_minutes');

        /** @var \Illuminate\Support\Collection $hangingServers */
        $hangingServers = Server::query()
            ->status(
                ServerStatus::New,
                ServerStatus::Starting,
                ServerStatus::Stopping,
            )

            ->where('status_updated_at', '<=', now()->subMinutes($thresholdInMinutes))
            ->get();

        if ($hangingServers->isEmpty()) {
            $this->components->info('No hanging servers detected');

            return self::SUCCESS;
        }

        $this->components->warn("Detected {$hangingServers->count()} hanging ".Str::plural('server', $hangingServers->count()));

        $hangingServers->each(function (Server $server) {
            $server->markAsHanging();
        });

        return self::SUCCESS;
    }
}

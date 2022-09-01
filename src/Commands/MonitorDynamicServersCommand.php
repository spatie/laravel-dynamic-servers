<?php

namespace Spatie\DynamicServers\Commands;

use Illuminate\Console\Command;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;

class MonitorDynamicServersCommand extends Command
{
    public $signature = 'dynamic-servers:monitor';

    public function handle()
    {
        $this->info('Monitoring dynamic servers');

        $initialServerCounts = Server::countPerStatus();

        DynamicServers::monitor();

        $currentServerCounts = Server::countPerStatus();

        $this->summarizeDifference($initialServerCounts, $currentServerCounts);

        return self::SUCCESS;
    }

    protected function summarizeDifference(array $initialCounts, array $currentCounts): self
    {
        $differences = collect($initialCounts)
            ->map(fn (int $count, string $status) => $currentCounts[$status] - $count)
            ->reject(fn (int $count) => $count === 0);

        if ($differences->isEmpty()) {
            $this->components->info('No servers started or stopped');

            return $this;
        }

        $differences->each(function (int $count, string $status) {
            $this->components->twoColumnDetail($status, (string) $count);
        });

        return $this;
    }
}

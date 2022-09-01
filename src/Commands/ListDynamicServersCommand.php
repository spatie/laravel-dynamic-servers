<?php

namespace Spatie\DynamicServers\Commands;

use Illuminate\Console\Command;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class ListDynamicServersCommand extends Command
{
    public $signature = 'dynamic-servers:list';

    public function handle()
    {
        $this->line('');

        $headers = [
            'Name',
            'Provider',
            'Type',
            'Status',
            'Status updated at',
        ];

        $rows = Server::query()
            ->whereNot('status', ServerStatus::Stopped)
            ->get();

        if ($rows->isEmpty()) {
            $this->components->info('No dynamic servers found...');

            return self::SUCCESS;
        }

        $rows = $rows
            ->map(function (Server $server) {
                return [
                    'name' => $server->name,
                    'provider' => $server->provider,
                    'type' => $server->type,
                    'status' => $server->status->value,
                    'status_updated_at' => $server->status_updated_at?->format('Y-m-d H:i:s') ?? 'Unknown',
                ];
            })
            ->all();

        $this->table($headers, $rows);

        $this->line('');

        return self::SUCCESS;
    }
}

<?php

use Spatie\DynamicServers\Commands\DetectHangingServersCommand;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;
use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function() {
    testTime()->freeze('2021-01-01 00:00:00');

    $threshold = config('dynamic-servers.mark_server_as_hanging_after_minutes');

    $this->server = Server::factory()->create([
        'status' => ServerStatus::Starting,
        'status_updated_at' => now()->subMinutes($threshold)->addSecond()
    ]);
});

it('can detect a hanging server', function() {
    artisan(DetectHangingServersCommand::class)->assertSuccessful();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Starting);

    testTime()->addSecond();
    artisan(DetectHangingServersCommand::class)->assertSuccessful();
    expect($this->server->refresh()->status)->toBe(ServerStatus::Hanging);
});

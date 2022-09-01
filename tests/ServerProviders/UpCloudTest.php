<?php

use Illuminate\Support\Str;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\ServerProviders\UpCloud\UpCloudServerProvider;

beforeEach(function () {
    if (! $this->upCloudHasBeenConfigured()) {
        $this->markTestSkipped('Up cloud not configured');
    }

    $server = Server::factory()->create()->addMeta('server_properties.uuid', Str::uuid());

    $this->upcloudServerProvider = (new UpCloudServerProvider())->setServer($server);
});

it('can determine the total number of servers on UpCloud', function () {
    expect($this->upcloudServerProvider->currentServerCount())->toBeInt();
});

it('can determine that the server has been deleted', function () {
    expect($this->upcloudServerProvider->hasBeenDeleted())->toBeTrue();
});

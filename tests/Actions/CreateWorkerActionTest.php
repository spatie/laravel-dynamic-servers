<?php

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Laravel\Horizon\Events\LongWaitDetected;
use Spatie\DynamicServers\Jobs\VerifyServerStartedJob;
use Spatie\DynamicServers\Jobs\CreateWorkerJob;
use Spatie\DynamicServers\Jobs\DeleteWorkerJob;

beforeEach(function () {
    $fakeUuid = getStub('server');
    $fakeUuid['server']['uuid'] = 'some-fake-uuid';

    $maintenance = getStub('server');
    $maintenance['server']['uuid'] = 'some-fake-uuid';
    $maintenance['server']['state'] = 'maintenance';

    $started = getStub('server');
    $started['server']['uuid'] = 'some-fake-uuid';
    $started['server']['state'] = 'started';

    Http::preventStrayRequests()
        ->fake([
            'https://api.upcloud.com/1.3/server' => Http::response($fakeUuid),
            'https://api.upcloud.com/1.3/server/some-fake-uuid' => Http::sequence([
                Http::response($maintenance),
                Http::response($started),
            ]),
        ]);
});

it('fires when a long wait time is detected', function () {
    Queue::fake(CreateWorkerJob::class);

    event(new LongWaitDetected('redis', array_key_first(config('horizon.waits')), 600));

    Queue::assertPushed(CreateWorkerJob::class);
});

it('creates a new cloned worker on upcloud', function () {
    Queue::fake();

    dispatch(new CreateWorkerJob());
    processQueuedJobs();

    expect(Cache::get('creating-worker'))->toBeTrue();
    Queue::assertPushed(VerifyServerStartedJob::class, function (VerifyServerStartedJob $job) {
        expect($job->serverUuid)->toBe('some-fake-uuid');

        return true;
    });
});

it('does nothing when already creating a worker', function () {
    Queue::fake();

    Cache::put('creating-worker', true);

    dispatch(new CreateWorkerJob());
    processQueuedJobs();

    Queue::assertNotPushed(VerifyServerStartedJob::class);
});

it('checks worker status and releases when state is maintenance', function () {
    Queue::after(function (JobProcessed $event) {
        $this->assertTrue($event->job->isReleased());
    });

    dispatch(new VerifyServerStartedJob('some-fake-uuid'));
});

it('checks worker status and deletes worker when completed', function () {
    Date::setTestNow($now = now());

    Queue::fake();
    Cache::put('creating-worker', true);
    Cache::put('workers', []);

    app()->call([(new VerifyServerStartedJob('some-fake-uuid')), 'handle']); // First time it's not ready yet
    app()->call([(new VerifyServerStartedJob('some-fake-uuid')), 'handle']);

    expect(Cache::get('creating-worker'))->toBeFalse();
    expect(Cache::get('workers'))->toBe([
        'some-fake-uuid',
    ]);

    Queue::assertPushed(DeleteWorkerJob::class, function (DeleteWorkerJob $job) use ($now) {
        expect($job->delay->diffInMinutes($now))->toBe(30);

        return true;
    });
});

function processQueuedJobs()
{
    foreach (Queue::pushedJobs() as $job) {
        app()->call([$job[0]['job'], 'handle']);
    }
}
